<?php

namespace App\Service;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use RuntimeException;

class LocalUploadService
{
    /**
     * 保存到 public/uploads/{dir}/Y/m 目录，返回媒体元数据。
     *
     * @return array{file_url: string, file_name: string, file_key: string, storage_provider: string, extension: string, file_size: int, file_type: string}
     */
    public function store(UploadedFile $file, string $dir = 'products'): array
    {
        $extension = strtolower((string) $file->getClientOriginalExtension());
        $mime = (string) ($file->getClientMimeType() ?: '');
        $size = (int) $file->getSize();
        $original = (string) $file->getClientOriginalName();

        $relativeDir = 'uploads/'.trim($dir, '/').'/'.date('Y/m');
        $filename = Str::lower(Str::ulid()->toBase32()).($extension !== '' ? '.'.$extension : '');
        $relativePath = $relativeDir.'/'.$filename;
        $absoluteDir = public_path($relativeDir);

        if (! is_dir($absoluteDir) && ! mkdir($absoluteDir, 0755, true) && ! is_dir($absoluteDir)) {
            throw new RuntimeException('创建上传目录失败');
        }

        $file->move($absoluteDir, $filename);

        return [
            'file_url' => '/'.$relativePath,
            'file_name' => $original,
            'file_key' => $relativePath,
            'storage_provider' => 'local',
            'extension' => $extension,
            'file_size' => $size,
            'file_type' => $mime,
        ];
    }
}
