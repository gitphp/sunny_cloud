<?php

namespace App\Service;

use App\Constants\Code\CodePrefix;
use App\Constants\Code\SiteConfigError;
use App\Enums\OperationAction;
use App\Enums\OperationBizType;
use App\Enums\SiteConfigGroup;
use App\Enums\SiteConfigInputType;
use App\Exceptions\BusinessException;
use App\Models\SiteConfig;
use Illuminate\Support\Facades\DB;

class SiteConfigService
{
    public function __construct(
        private readonly OperationLogService $operationLogService
    ) {
    }

    public function listGrouped(?string $group = null): array
    {
        $query = SiteConfig::query()->orderBy('conf_sort')->orderBy('id');

        if ($group !== null && $group !== '') {
            $query->where('conf_group', $group);
        }

        $items = $query->get()->map(fn (SiteConfig $item) => $this->toArray($item))->values()->all();

        $grouped = [];
        foreach ($items as $item) {
            $grouped[$item['conf_group']][] = $item;
        }

        return [
            'list' => $items,
            'grouped' => $grouped,
            'options' => [
                'conf_group' => SiteConfigGroup::labels(),
                'input_type' => SiteConfigInputType::labels(),
            ],
        ];
    }

    public function create(array $data): SiteConfig
    {
        $key = (string) $data['conf_key'];
        $this->assertKeyUnique($key);

        $config = SiteConfig::query()->create([
            'conf_group' => (string) ($data['conf_group'] ?? SiteConfigGroup::Basic->value),
            'conf_key' => $key,
            'conf_value' => (string) ($data['conf_value'] ?? ''),
            'conf_desc' => (string) ($data['conf_desc'] ?? ''),
            'input_type' => (string) ($data['input_type'] ?? SiteConfigInputType::Text->value),
            'conf_sort' => (int) ($data['conf_sort'] ?? 0),
        ]);

        $this->operationLogService->logCrud(
            OperationAction::Insert,
            OperationBizType::SiteConfig,
            'site_config_created',
            $config->id,
            $config->conf_key,
            null,
            $this->toArray($config),
            'SiteConfigService@create'
        );

        return $config;
    }

    public function update(SiteConfig $config, array $data): SiteConfig
    {
        $key = (string) ($data['conf_key'] ?? $config->conf_key);
        $this->assertKeyUnique($key, (string) $config->id);

        $old = $this->toArray($config);

        $config->fill([
            'conf_group' => (string) ($data['conf_group'] ?? $config->conf_group),
            'conf_key' => $key,
            'conf_value' => (string) ($data['conf_value'] ?? $config->conf_value),
            'conf_desc' => (string) ($data['conf_desc'] ?? $config->conf_desc),
            'input_type' => (string) ($data['input_type'] ?? $config->input_type),
            'conf_sort' => (int) ($data['conf_sort'] ?? $config->conf_sort),
        ]);
        $config->save();
        $config = $config->fresh();

        $this->operationLogService->logCrud(
            OperationAction::Update,
            OperationBizType::SiteConfig,
            'site_config_updated',
            $config->id,
            $config->conf_key,
            $old,
            $this->toArray($config),
            'SiteConfigService@update'
        );

        return $config;
    }

    /**
     * 批量保存配置值：[{id, conf_value}] 或 {conf_key: value}
     */
    public function batchUpdateValues(array $items): array
    {
        if ($items === []) {
            throw new BusinessException($this->code(SiteConfigError::BATCH_INVALID), '没有可保存的配置');
        }

        return DB::transaction(function () use ($items) {
            $updated = [];

            foreach ($items as $item) {
                if (! is_array($item)) {
                    continue;
                }

                $config = null;
                if (! empty($item['id'])) {
                    $config = SiteConfig::query()->find($item['id']);
                } elseif (! empty($item['conf_key'])) {
                    $config = SiteConfig::query()->where('conf_key', $item['conf_key'])->first();
                }

                if (! $config) {
                    continue;
                }

                $config->conf_value = (string) ($item['conf_value'] ?? '');
                $config->save();
                $updated[] = $this->toArray($config->fresh());
            }

            if ($updated === []) {
                throw new BusinessException($this->code(SiteConfigError::BATCH_INVALID), '没有有效的配置项');
            }

            $this->operationLogService->logCrud(
                OperationAction::Update,
                OperationBizType::SiteConfig,
                'site_config_batch_updated',
                0,
                'batch_'.count($updated),
                null,
                ['count' => count($updated), 'keys' => array_column($updated, 'conf_key')],
                'SiteConfigService@batchUpdateValues'
            );

            return $updated;
        });
    }

    public function delete(SiteConfig $config): void
    {
        $old = $this->toArray($config);
        $config->delete();

        $this->operationLogService->logCrud(
            OperationAction::Delete,
            OperationBizType::SiteConfig,
            'site_config_deleted',
            $old['id'],
            $old['conf_key'],
            $old,
            null,
            'SiteConfigService@delete'
        );
    }

    public function toArray(SiteConfig $config): array
    {
        return [
            'id' => (string) $config->id,
            'conf_group' => $config->conf_group,
            'conf_group_label' => SiteConfigGroup::tryFrom($config->conf_group)?->label() ?? $config->conf_group,
            'conf_key' => $config->conf_key,
            'conf_value' => $config->conf_value,
            'conf_desc' => $config->conf_desc,
            'input_type' => $config->input_type,
            'input_type_label' => SiteConfigInputType::tryFrom($config->input_type)?->label() ?? $config->input_type,
            'conf_sort' => (int) $config->conf_sort,
            'created_at' => optional($config->created_at)?->format('Y-m-d H:i:s'),
            'updated_at' => optional($config->updated_at)?->format('Y-m-d H:i:s'),
        ];
    }

    private function assertKeyUnique(string $key, ?string $excludeId = null): void
    {
        $exists = SiteConfig::query()
            ->where('conf_key', $key)
            ->when($excludeId, fn ($q) => $q->where('id', '!=', $excludeId))
            ->exists();

        if ($exists) {
            throw new BusinessException($this->code(SiteConfigError::KEY_DUPLICATED), '配置键名已存在');
        }
    }

    private function code(int $error): int
    {
        return CodePrefix::SITE_CONFIG * 1000 + $error;
    }
}
