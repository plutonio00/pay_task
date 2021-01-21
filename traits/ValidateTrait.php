<?php
namespace app\traits;

use yii\db\ActiveRecord;

trait ValidateTrait
{
    /**
     * @param string $attribute
     * @param ?array $params
     */
    public function validateField(string $attribute, ?array $params): void
    {
        /** @var ActiveRecord $this */
        if ($this->isAttributeChanged($attribute)) {
            $validateMethod = $params['method'] ?? 'validate' . $this->toCamelCase($attribute);

            !empty($params['args']) ?
                $this->$validateMethod($attribute, $params['args']) : $this->$validateMethod($attribute);
        }
    }

    /**
     * @param string $attribute
     * @return string|string[]
     */
    private function toCamelCase(string $attribute) {
        return str_replace('_', '', ucwords($attribute));
    }
}