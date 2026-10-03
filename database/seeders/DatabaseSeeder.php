<?php

namespace Database\Seeders;

use App\Models\ActivityHistory;
use App\Models\Brief;
use App\Models\CreativeTask;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $pw = Hash::make('Password123!');
        $mk = fn (string $name, string $email, string $role, bool $active = true) => User::updateOrCreate(
            ['email' => $email],
            ['name' => $name, 'password' => $pw, 'role' => $role, 'is_active' => $active]
        );

        // 4 akun demo (password: Password123!)
        $admin = $mk('Fanny Maharani', 'admin@dthree.test', 'admin');
        $user = $mk('Andi Pratama', 'user@dthree.test', 'user');
        $mkt = $mk('Rani Putri', 'marketing@dthree.test', 'marketing');
        $creative = $mk('Tim Kreatif', 'creative@dthree.test', 'creative');

        // Akun tambahan agar data terlihat hidup
        $siti = $mk('Siti Rahma', 'siti@dthree.test', 'user');
        $budi = $mk('Budi Santoso', 'budi@dthree.test', 'user');
        $dewi = $mk('Dewi Lestari', 'dewi@dthree.test', 'marketing');
        $agus = $mk('Agus Setiawan', 'agus@dthree.test', 'creative', false);

        if (Brief::count() > 0) {
            return; // jangan duplikasi data dummy
        }

        // [file, platform, brand, pengirim, status, hari lalu, jam, catatan]
        $rows = [
            ['Brief_Campaign_Product_Launch.pdf', 'instagram_post', 'dthree', $user, 'pending', 0, 14, null],
            ['Brief_Reels_Edukasi_Skincare.pdf', 'tiktok', 'dthree', $siti, 'approved', 1, 9, null],
            ['Brief_Konten_Promo_Ramadhan.pdf', 'instagram_reel', 'hurim', $budi, 'rejected', 2, 11, 'Ganti visual sesuai feedback brand guideline.'],
            ['Brief_Carousel_Product_Launch.pdf', 'instagram_post', 'asfara', $user, 'approved', 3, 16, null],
            ['Brief_Story_Behind_The_Scene.pdf', 'tiktok', 'dthree', $siti, 'pending', 4, 10, null],
            ['Brief_Testimoni_Customer.pdf', 'instagram_story', 'hurim', $budi, 'approved', 5, 15, null],
            ['Brief_Promo_Spesial_Hari_Raya.pdf', 'tiktok', 'asfara', $user, 'approved', 5, 9, null],
            ['Brief_Product_Highlight.pdf', 'instagram_post', 'dthree', $siti, 'rejected', 6, 13, 'Perlu revisi konsep dan tambahkan call to action.'],
            ['Brief_Tips_Trick_Skincare.pdf', 'instagram_reel', 'hurim', $budi, 'approved', 6, 11, null],
            ['Brief_Brand_Awareness.pdf', 'instagram_post', 'dthree', $user, 'pending', 0, 9, null],
        ];

        $taskStates = ['pending', 'in_progress', 'completed', 'in_progress', 'completed']; // untuk brief approved

        $approvedIndex = 0;
        foreach ($rows as [$file, $platform, $brand, $sender, $status, $daysAgo, $hour, $note]) {
            $at = now()->subDays($daysAgo)->setTime($hour, rand(0, 59));
            $path = 'briefs/'.Str::uuid().'.pdf';
            $content = $this->samplePdf(str_replace('_', ' ', pathinfo($file, PATHINFO_FILENAME)), Brief::BRANDS[$brand], Brief::PLATFORMS[$platform]);
            Storage::disk('local')->put($path, $content);

            $brief = Brief::create([
                'user_id' => $sender->id, 'file_path' => $path, 'original_filename' => $file,
                'file_size' => strlen($content) + rand(900000, 3200000), 'platform' => $platform, 'brand' => $brand,
                'status' => $status, 'rejection_note' => $note,
                'reviewed_by' => $status === 'pending' ? null : $mkt->id,
                'reviewed_at' => $status === 'pending' ? null : $at->copy()->addHours(2),
                'created_at' => $at, 'updated_at' => $at,
            ]);

            $this->log('brief_uploaded', sprintf('Brief baru "%s" diupload oleh %s', $brief->title, $sender->name), $sender, $brief, null, $at);

            if ($status === 'approved') {
                $state = $taskStates[$approvedIndex++ % count($taskStates)];
                $approvedAt = $at->copy()->addHours(2);
                $task = CreativeTask::create([
                    'brief_id' => $brief->id,
                    'assigned_to' => $state === 'pending' ? null : $creative->id,
                    'status' => $state,
                    'started_at' => $state === 'pending' ? null : $approvedAt->copy()->addHours(3),
                    'completed_at' => $state === 'completed' ? $approvedAt->copy()->addDay() : null,
                    'created_at' => $approvedAt, 'updated_at' => $approvedAt,
                ]);
                $this->log('brief_approved', sprintf('Brief "%s" disetujui oleh %s dan masuk ke task kreatif', $brief->title, $mkt->name), $mkt, $brief, $task, $approvedAt);
                if ($state !== 'pending') {
                    $this->log('task_in_progress', sprintf('Task "%s" dipindahkan ke Sedang Diproses oleh %s', $brief->title, $creative->name), $creative, $brief, $task, $approvedAt->copy()->addHours(3));
                }
                if ($state === 'completed') {
                    $this->log('task_completed', sprintf('Task "%s" ditandai Selesai oleh %s', $brief->title, $creative->name), $creative, $brief, $task, $approvedAt->copy()->addDay());
                }
            } elseif ($status === 'rejected') {
                $rt = $at->copy()->addHours(2);
                $this->log('brief_rejected', sprintf('Brief "%s" ditolak oleh %s', $brief->title, $mkt->name), $mkt, $brief, null, $rt);
                $this->log('note_added', sprintf('Catatan untuk "%s": %s', $brief->title, $note), $mkt, $brief, null, $rt->copy()->addMinute());
            }
        }

        $this->log('user_created', 'User baru Budi Santoso ditambahkan oleh '.$admin->name, $admin, null, null, now()->subDays(6));
    }

    private function log(string $action, string $desc, ?User $user, ?Brief $brief, ?CreativeTask $task, $at): void
    {
        ActivityHistory::create([
            'user_id' => $user?->id, 'brief_id' => $brief?->id, 'creative_task_id' => $task?->id,
            'action' => $action, 'description' => $desc, 'created_at' => $at,
        ]);
    }

    /** PDF satu halaman yang valid (tanpa library) untuk data demo. */
    private function samplePdf(string $title, string $brand, string $platform): string
    {
        $esc = fn (string $s) => str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], preg_replace('/[^\x20-\x7E]/', '', $s));
        $lines = [
            [24, 760, $title],
            [12, 730, 'BRIEFLY - Contoh Brief Konten (data demo)'],
            [12, 700, 'Brand    : '.$brand],
            [12, 680, 'Platform : '.$platform],
            [12, 640, 'Tujuan   : Meningkatkan awareness dan engagement.'],
            [12, 620, 'Pesan    : Sampaikan keunggulan produk secara singkat dan jelas.'],
            [12, 600, 'Catatan  : Dokumen ini dibuat otomatis oleh seeder.'],
        ];
        $stream = '';
        foreach ($lines as [$size, $y, $text]) {
            $stream .= "BT /F1 $size Tf 56 $y Td (".$esc($text).") Tj ET\n";
        }

        $objs = [
            '<< /Type /Catalog /Pages 2 0 R >>',
            '<< /Type /Pages /Kids [3 0 R] /Count 1 >>',
            '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Contents 4 0 R /Resources << /Font << /F1 5 0 R >> >> >>',
            '<< /Length '.strlen($stream)." >>\nstream\n".$stream.'endstream',
            '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>',
        ];

        $pdf = "%PDF-1.4\n";
        $offsets = [];
        foreach ($objs as $i => $o) {
            $offsets[] = strlen($pdf);
            $pdf .= ($i + 1)." 0 obj\n".$o."\nendobj\n";
        }
        $xref = strlen($pdf);
        $pdf .= "xref\n0 ".(count($objs) + 1)."\n0000000000 65535 f \n";
        foreach ($offsets as $off) {
            $pdf .= sprintf("%010d 00000 n \n", $off);
        }
        $pdf .= "trailer\n<< /Size ".(count($objs) + 1)." /Root 1 0 R >>\nstartxref\n$xref\n%%EOF";

        return $pdf;
    }
}
