<?php

namespace App\Actions;

class MergeIntoSubArrayAction
{

    /**
     * Merge an array into an sub array of target array
     *
     * @param array $target_array
     * @param array $sub_arrray
     * @param string $target_key Common key to check in both $sub_array and $target_array
     * @param string $sub_key Named key for the $target_array
     *
     * @return void
     */
    public function handle(array $target_array, array $sub_arrray, string $target_key, string $sub_key)
    {

        foreach ($target_array as &$target_array_key) {
            foreach ($sub_arrray as $key => $value) {
                if ($target_array_key[$target_key] === $value[$target_key]) {
                    $target_array_key[$sub_key] = $value;
                    unset($sub_arrray[$key]);
                }
            }
        }

        return $target_array;
    }
}
