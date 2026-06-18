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
        if (!is_array($stores)) {
            if ($stores === null || $stores === '') {
                $stores = [0]; // no store field in form — default to all stores
            } else {
                $stores = explode(',', (string)$stores);
            }
        }
        if (empty($stores)) {
            $stores = [0]; // empty submission defaults to all stores
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
