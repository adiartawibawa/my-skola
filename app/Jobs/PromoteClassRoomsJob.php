<?php

namespace App\Jobs;

use App\Actions\Academic\PromoteClassRoomAction;
use App\Actions\Academic\ResolveNextClassRoomAction;
use App\Models\AcademicYear;
use App\Models\ClassRoom;
use App\Models\User;
use Filament\Notifications\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Validation\ValidationException;

class PromoteClassRoomsJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * @param  array<int, string>  $classRoomIds  ID kelas sumber yang akan diproses
     * @param  string  $targetAcademicYearId  ID Tahun Akademik tujuan
     * @param  string|null  $notifiableId  ID user yang memicu proses, untuk notifikasi hasil (opsional)
     */
    public function __construct(
        public array $classRoomIds,
        public string $targetAcademicYearId,
        public ?string $notifiableId = null,
    ) {}

    public function handle(
        ResolveNextClassRoomAction $resolveNextClassRoom,
        PromoteClassRoomAction $promoteClassRoom,
    ): void {
        $targetAcademicYear = AcademicYear::query()->findOrFail($this->targetAcademicYearId);

        $totalPromoted = 0;
        $skipped = [];
        $failed = [];

        // withoutGlobalScopes(): job ini berjalan async dan menerima ID
        // kelas eksplisit dari saat bulk action dijalankan — tidak boleh
        // diam-diam gagal menemukan kelasnya kalau tahun aktif kebetulan
        // sudah berpindah sebelum job diproses.
        ClassRoom::query()
            ->withoutGlobalScopes()
            ->whereIn('id', $this->classRoomIds)
            ->with('programKeahlian')
            ->each(function (ClassRoom $source) use ($targetAcademicYear, $resolveNextClassRoom, $promoteClassRoom, &$totalPromoted, &$skipped, &$failed) {
                try {
                    $target = $resolveNextClassRoom->execute($source, $targetAcademicYear);

                    if (! $target) {
                        // Kelas tingkat akhir — harus diluluskan lewat
                        // GraduateClassRoomAction, bukan dipromosikan.
                        $skipped[] = $source->full_name;

                        return;
                    }

                    $totalPromoted += $promoteClassRoom->execute($source, $target);
                } catch (ValidationException $e) {
                    // Satu kelas gagal validasi (mis. kombinasi kelas
                    // tujuan bentrok) TIDAK BOLEH menghentikan proses
                    // kelas lain dalam batch yang sama — dicatat sebagai
                    // gagal, lanjut ke kelas berikutnya.
                    $failed[] = $source->full_name.': '.collect($e->errors())->flatten()->implode(' ');
                }
            });

        $this->notifyResult($totalPromoted, $skipped, $failed);
    }

    protected function notifyResult(int $totalPromoted, array $skipped, array $failed = []): void
    {
        if (! $this->notifiableId) {
            return;
        }

        $notifiable = User::query()->find($this->notifiableId);

        if (! $notifiable) {
            return;
        }

        $body = "{$totalPromoted} siswa berhasil dinaikkan ke tahun ajaran berikutnya.";

        if (! empty($skipped)) {
            $body .= ' Kelas tingkat akhir dilewati (perlu diluluskan manual): '.implode(', ', $skipped).'.';
        }

        if (! empty($failed)) {
            $body .= ' Gagal diproses: '.implode('; ', $failed).'.';
        }

        Notification::make()
            ->title('Proses Kenaikan Kelas Selesai')
            ->body($body)
            ->when(! empty($failed), fn (Notification $notification) => $notification->warning(), fn (Notification $notification) => $notification->success())
            ->sendToDatabase($notifiable);
    }
}
