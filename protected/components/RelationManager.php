<?php

/**
 * Relation manager class
 */
class RelationManager {
    /**
     * Chart's cell types
     */
    const MX_GRAPH_CELL_TYPE_CHECK  = 'check';
    const MX_GRAPH_CELL_TYPE_FILTER = 'filter';

    /**
     * Filters list
     * @var array
     */
    public static $filters = array(
        array(
            "name" => PortFilter::ID,
            "title" => PortFilter::TITLE
        )
    );

    /**
     * Returns start check
     * @param $node
     * @return bool
     */
    public static function getStartCheck($node) {
        $cell = $node->xpath("//*[@type='check' and @start_check='1']");

        if (!$cell[0]) {
            return false;
        }

        return $cell[0];
    }

    /**
     * Returns relation's check ids
     * @param $node
     * @return array
     */
    public static function getCheckIds($node) {
        $ids = array();
        $cells = $node->xpath("//*[@type='check']");

        foreach ($cells as $cell) {
            $attributes = $cell->attributes();

            $ids[] = (int) $attributes->check_id;
        }

        return $ids;
    }

    /**
     * Returns cell by id
     * @param $node
     * @param $id
     * @return bool
     */
    public static function getCell($node, $id) {
        $cell = $node->xpath("//*[@type and @id=$id]");

        if (!isset($cell[0]) || !$cell[0]) {
            throw new Exception("Cell not found");
        }

        return $cell[0];
    }

    /**
     * Returns cell's connections
     * @param $node
     * @param $id
     * @return mixed
     */
    public static function getCellConnections($node, $id) {
        return $node->xpath("//*[@type='connection' and @source=$id]");
    }

    /**
     * Returns cell's children count
     * @param $node
     * @param $id
     * @param null $count
     * @return int|null
     */
    public static function getCellChildrenCount($node, $id, $count=null) {
        $startCell = false;

        if ($count === null) {
            $startCell = true;
            $count = 0;
        }

        $n = $count;
        $edges = RelationManager::getCellConnections($node, $id);

        foreach ($edges as $edge) {
            $targetId = (int) $edge->attributes()->target;
            $n += self::getCellChildrenCount($node, $targetId, $count);
        }

        if (!$startCell) {
            $n++;
        }

        return $n;
    }

    /**
     * Apply relation template filter to result
     * @param $filter
     * @param $values
     * @param $input
     * @return array|null
     * @throws Exception
     */
    public static function applyFilter($filter, $values, $input) {
        $result = null;

        switch ($filter) {
            case PortFilter::ID:
                $ports = explode(",", $values);

                foreach ($ports as $key => $value) {
                    $port = intval($value);

                    if (!$port) {
                        throw new Exception("Invalid filter values");
                    }

                    $ports[$key] = $port;
                }

                $targets = explode("\n", $input);
                $result = PortFilter::apply($ports, $targets);
                $result = implode("\n", $result);

                break;
            default:
                throw new Exception("Unknown filter.");

                break;
        }

        return $result;
    }

    /**
     * Validate relations
     * @param $data
     * @param Target $target
     * @throws Exception
     */
    public static function validateRelations($data, Target $target=null) {
        try {
            $relations = new SimpleXMLElement($data, LIBXML_NOERROR);
        } catch (Exception $e) {
            throw new Exception("Relations are not valid.");
        }

        $checkNodes = $relations->xpath('//*[@type="check"]');
        $startCheckId = false;

        $checkIds = array();

        foreach ($checkNodes as $node) {
            $attributes = $node->attributes();
            $checkId = (int) $attributes->check_id;

            if (!$checkId) {
                throw new Exception("There are blocks with no checks tied.");
            }

            $checkIds[] = $checkId;

            if ((int) $attributes->start_check == 1) {
                $startCheckId = $attributes->id;
            }
        }

        if (!$startCheckId) {
            throw new Exception("Start check is not defined.");
        }

        if ($target) {
            $criteria = new CDbCriteria();
            $criteria->addInCondition("check_id", $checkIds);
            $criteria->addColumnCondition(array(
                "target_id" => $target->id
            ));

            $targetCheckCount = TargetCheck::model()->count($criteria);

            if ($targetCheckCount < count($checkIds)) {
                throw new Exception("Not all relation checks are attached to target.");
            }
        }

        // Check if graph has more than one connection group
        $cellCount = count($relations->xpath('//*[@type="check" or @type="filter"]'));
        $startCheckChildren = RelationManager::getCellChildrenCount($relations, $startCheckId);

        if ($cellCount > $startCheckChildren + 1) {
            throw new Exception("Template has more than one connection group.");
        }
    }
}