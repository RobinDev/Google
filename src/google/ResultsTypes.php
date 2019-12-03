<?php

namespace rOpenDev\Google;

class ResultsTypes
{
    protected $types;

    public static function get()
    {
        $current = new self();

        return $current->getTypes();
    }

    public function getTypes()
    {
        if (null === $this->types) {
            $file = __DIR__.'/ResultTypes.csv';
            $content = file_get_contents($file);
            $rows = explode(chr(10), $content);
            array_shift($rows);
            foreach ($rows as $row) {
                $row = explode(',', $row);
                if (isset($row[1])) {
                    $this->types[strtolower($row[0])] = $row[1];
                }
            }
        }

        return $this->types;
    }
}
