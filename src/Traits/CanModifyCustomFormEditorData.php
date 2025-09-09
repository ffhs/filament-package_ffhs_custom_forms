<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Traits;

//ToDo Update or Delete
trait CanModifyCustomFormEditorData
{

//    /**
//     * @throws Exception
//     */
//    protected function removeFieldFromEditorData(string $toRemoveKey, array $fields): array
//    {
//        //Delete Structure
//        $toDelete = $fields[$toRemoveKey];
//
//        $nestedList = NestedFlattenList::make($fields, CustomField::class);
//        $nestedList->removeFromPosition($toDelete['form_position']);
//
//        return $nestedList->getData();
//    }
//
//    /**
//     * @throws Exception
//     */
//    protected function addMultipleFieldsToEditorData(array $toAddFields, int $position, array $fields): array
//    {
//        $nestedList = NestedFlattenList::make($fields, CustomField::class);
//        $nestedList->addManyOnPosition($position, $toAddFields, true);
//
//        return $nestedList->getData();
//    }
//
//    protected function getUsedGeneralFieldIds(array $customFields, CustomForm $form): array
//    {
//        //GeneralFieldIds From GeneralFields
//        $generalFields = array_filter($customFields, fn($fieldData) => !empty($fieldData['general_field_id']));
//        $generalFieldId = array_map(fn($used) => $used['general_field_id'], $generalFields);
//
//        //GeneralFieldIds From Templates
//        $templateData = array_filter($customFields, fn($fieldData) => !empty($fieldData['template_id']));
//        $templateIds = array_map(fn($used) => $used['template_id'], $templateData);
//
//        foreach ($templateIds as $templateId) {
//            $genFields = $form
//                ->getFormConfiguration()
//                ->getAvailableTemplates()
//                ->get($templateId)
//                ->ownedGeneralFields
//                ->pluck('id')
//                ->toArray();
//            $generalFieldId = [
//                ...$generalFieldId,
//                ...$genFields,
//            ];
//        }
//
//        return $generalFieldId;
//    }
}
