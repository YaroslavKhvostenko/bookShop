<?php
declare(strict_types=1);

namespace Models\AbstractProjectModels\Validation\Data\Basket\Update;

use Models\AbstractProjectModels\Validation\Data\Basket\AbstractValidator as BaseValidator;

abstract class AbstractValidator extends BaseValidator
{
   public function emptyCheck(array $data): array
   {
       $result = [];
       $arrayUnique = array_unique($data);
       if (count($arrayUnique) === 1 && in_array('', $arrayUnique)) {
           $result['empty_data'] = '';
       } else {
           foreach ($data as $productId => $productQuantity) {
               if ($productQuantity !== '') {
                   $result[$productId] = $productQuantity;
               }
           }
       }

       return $result;
   }

   public function correctCheck(array $data): ?array
   {
       $result = [];
       $updateData = [];
       foreach ($data as $productId => $productQuantity) {
           if (!is_numeric($productId) || !is_numeric($productQuantity)) {
               throw new \Exception(
                   '$productId and $productQuantity should be a numeric, not a word!' .
                   'You received $productId : ' . "'$productId'" . ', and ' .
                   '$productQuantity : ' . "'$productQuantity'" . ' !'
               );
           }

           $updateData[(int)$productId] = (int)$productQuantity;
       }

       if (in_array(0, $updateData)) {
           $result['zero_quantity'] = '';
       } else {
           foreach ($updateData as $productId => $productQuantity) {
               $result[$productId]['quantity'] = $productQuantity;
           }
       }

       return $result;
   }
}
