<?php
/**
 * Shared helpers for per-store visibility tables. Used by the Category, Tag and
 * Author resource models so saving an entity writes its store_id rows and
 * loading it restores them for the admin form.
 */
declare(strict_types=1);

namespace Etechflow\Blog\Model\ResourceModel;

trait StoreAwareTrait
{
    /**
     * @param int|int[]|string|null $stores
     */
    protected function syncStores(string $table, string $idColumn, int $id, $stores): void
    {
        if ($stores === null) {
            return; // field not submitted — leave existing rows untouched
        }
        if (!is_array($stores)) {
            $stores = $stores === '' ? [] : explode(',', (string)$stores);
        }
        $connection = $this->getConnection();
        $tableName = $this->getTable($table);
        $connection->delete($tableName, [$idColumn . ' = ?' => $id]);
        foreach (array_unique(array_map('intval', $stores)) as $storeId) {
            $connection->insert($tableName, [$idColumn => $id, 'store_id' => $storeId]);
        }
    }

    /**
     * @return int[]
     */
    protected function readStores(string $table, string $idColumn, int $id): array
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from($this->getTable($table), 'store_id')
            ->where($idColumn . ' = ?', $id);
        return array_map('intval', $connection->fetchCol($select));
    }
}
