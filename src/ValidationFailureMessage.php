<?php
/**
 * @see       https://github.com/zendframework/zend-datavalidator for the canonical source repository
 * @copyright Copyright (c) 2017 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/zendframework/zend-datavalidator/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Zend\DataValidator;

/**
 * Value object describing a validation failure message.
 */
final class ValidationFailureMessage
{
    /** @var string */
    private $code;

    /** @var string */
    private $template;

    /** @var array */
    private $variables;

    public function __construct(string $code, string $template, array $variables = [])
    {
        $this->code = $code;
        $this->template = $template;
        $this->variables = $variables;
    }

    public function getCode() : string
    {
        return $this->code;
    }

    public function getTemplate() : string
    {
        return $this->template;
    }

    public function getVariables() : array
    {
        return $this->variables;
    }
}
