<?php
/**
 * @see       https://github.com/zendframework/zend-datavalidator for the canonical source repository
 * @copyright Copyright (c) 2017 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/zendframework/zend-datavalidator/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Zend\DataValidator;

final class ValidatorChain implements ValidatorInterface
{
    /**
     * Array of validators.
     *
     * Each entry contains two properties:
     *
     * - instance, the actual validator instance.
     * - breakChainOnFailure, a boolean indicating whether the next in the chain
     *   should be executed if this validator fails.
     *
     * @var array
     */
    private $validators = [];

    /**
     * Append a validator to the chain
     *
     * If $breakChainOnFailure is true, then if the validator fails, the next validator in the chain,
     * if one exists, will not be executed.
     *
     * @param  Validator $validator
     * @param  bool $breakChainOnFailure
     */
    public function attach(ValidatorInterface $validator, bool $breakChainOnFailure = false)
    {
        $this->validators[] = [
            'instance'            => $validator,
            'breakChainOnFailure' => $breakChainOnFailure,
        ];
    }

    /**
     * Returns true if and only if $value passes all validations in the chain
     *
     * Validators are run in the order in which they were added to the chain (FIFO).
     *
     * {@inheritDoc}
     */
    public function validate($value, $context = null) : ResultInterface
    {
        $results = new ResultAggregate($value);

        foreach ($this->validators as $element) {
            $validator = $element['instance'];
            $result = $validator->validate($value, $context);
            $results->push($result);
            if ($result->isValid()) {
                continue;
            }

            if ($element['breakChainOnFailure']) {
                break;
            }
        }

        return $results;
    }
}
