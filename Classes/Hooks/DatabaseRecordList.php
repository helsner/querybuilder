<?php

namespace T3G\Querybuilder\Hooks;

use T3G\Querybuilder\Parser\QueryParser;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Recordlist\RecordList\DatabaseRecordList as CoreDatabaseRecordList;

/**
 * Class DatabaseRecordList.
 */
class DatabaseRecordList
{

    /**
     * @param array $parameters parameters
     * @param string $table the current database table
     * @param int $pageId the records' page ID
     * @param array $additionalConstraints additional constraints
     * @param array $fieldList field list
     * @param CoreDatabaseRecordList $parentObject
     * @param QueryBuilder $queryBuilder
     *
     * @return QueryBuilder
     * @throws \InvalidArgumentException
     */
    public function buildQueryParametersPostProcess(
        array &$parameters,
        string $table,
        int $pageId,
        array $additionalConstraints,
        array $fieldList,
        CoreDatabaseRecordList $parentObject,
        QueryBuilder $queryBuilder
    ): QueryBuilder {
        if ($parentObject->table !== null && GeneralUtility::_GP('route') === '/web/list/') {
            $filter = GeneralUtility::_GP('query');
            if ($filter !== null) {
                $filter = json_decode($filter);
                if ($filter) {
                    $queryBuilder = GeneralUtility::makeInstance(QueryParser::class)
                        ->parse($filter, $queryBuilder);
                }
            }
        }
        return $queryBuilder;
    }
}
