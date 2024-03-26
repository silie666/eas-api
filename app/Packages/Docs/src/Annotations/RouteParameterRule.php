<?php

namespace Package\ApiDocs\Annotations;

use Illuminate\Validation\ValidationRuleParser;

class RouteParameterRule
{
    const TYPE_INT      = 'int32';
    const TYPE_BIGINT   = 'int64';
    const TYPE_FLOAT    = 'float';
    const TYPE_DOUBLE   = 'double';
    const TYPE_STRING   = 'string';
    const TYPE_BYTE     = 'byte';
    const TYPE_BINARY   = 'binary';
    const TYPE_BOOL     = 'boolean';
    const TYPE_DATE     = 'date';
    const TYPE_DATETIME = 'datetime';
    const TYPE_PASSWORD = 'password';
    const TYPE_ARRAY    = 'array';

    const PRIMITIVE_TYPE_INTEGER = 'integer';
    const PRIMITIVE_TYPE_NUMBER  = 'number';
    const PRIMITIVE_TYPE_STRING  = 'string';
    const PRIMITIVE_TYPE_BOOLEAN = 'boolean';
    const PRIMITIVE_TYPE_ARRAY   = 'array';

    /**
     * 数据类型
     *
     * @var string
     */
    public string $type = self::TYPE_STRING;

    /**
     * 原始数据类型
     *
     * @var string
     */
    public string $primitiveType = self::PRIMITIVE_TYPE_STRING;

    /**
     * 是否必传参数
     *
     * @var bool
     */
    public bool $required = false;

    /**
     * 参数是否允许null
     *
     * @var bool
     */
    public bool $nullable = false;

    /**
     * 示例值
     *
     * @var mixed
     */
    public mixed $example = 'string';

    /**
     * 最大值
     *
     * @var int|null
     */
    public ?int $max = null;

    /**
     * 最小值
     *
     * @var int|null
     */
    public ?int $min = null;

    /**
     * 规则描述
     *
     * @var array
     */
    public array $descriptions = [];

    /**
     * 原始类型对应
     *
     * @var array
     */
    protected array $primitiveTypeMap = [
        self::TYPE_INT      => self::PRIMITIVE_TYPE_INTEGER,
        self::TYPE_BIGINT   => self::PRIMITIVE_TYPE_INTEGER,
        self::TYPE_FLOAT    => self::PRIMITIVE_TYPE_NUMBER,
        self::TYPE_DOUBLE   => self::PRIMITIVE_TYPE_NUMBER,
        self::TYPE_STRING   => self::PRIMITIVE_TYPE_STRING,
        self::TYPE_BYTE     => self::PRIMITIVE_TYPE_STRING,
        self::TYPE_BINARY   => self::PRIMITIVE_TYPE_STRING,
        self::TYPE_BOOL     => self::PRIMITIVE_TYPE_BOOLEAN,
        self::TYPE_DATE     => self::PRIMITIVE_TYPE_STRING,
        self::TYPE_DATETIME => self::PRIMITIVE_TYPE_STRING,
        self::TYPE_PASSWORD => self::PRIMITIVE_TYPE_STRING,
        self::TYPE_ARRAY    => self::PRIMITIVE_TYPE_ARRAY,
    ];

    /**
     * RouteParameterRule constructor.
     *
     * @param array $rules
     */
    public function __construct(array $rules)
    {
        foreach ($rules as $rule) {
            if (is_string($rule)) {
                $this->parseRule($rule);
            }
        }
    }

    /**
     * 将规则转换成成员变量
     *
     * @param string $rule
     */
    protected function parseRule(string $rule): void
    {
        // TODO: 完成规则库
        [$rule, $parameters] = ValidationRuleParser::parse($rule);
        switch ($rule) {
            // Boolean
            case 'Accepted':
            case 'Boolean':
            case 'TcBool':
                $this->type    = self::TYPE_BOOL;
                $this->example = true;
                break;

            // Date
            case 'After':
            case 'AfterOrEqual':
            case 'Before':
            case 'BeforeOrEqual':
            case 'Date':
            case 'DateEquals':
                $this->type = self::TYPE_DATE;
                break;

            // String
            case 'Alpha':
            case 'AlphaDash':
            case 'AlphaNumeric':
            case 'Confirmed':
            case 'Ip':
            case 'Ipv4':
            case 'Ipv6':
            case 'Json':
            case 'String':
            case 'Url':
                $this->type = self::TYPE_STRING;
                break;
            case 'TcIsApprove':
                $this->type     = self::TYPE_STRING;
                $this->nullable = true;
                break;

            // Array
            case 'Array':
                $this->type = self::TYPE_ARRAY;
                break;

            // Int
            case 'Digits':
            case 'DigitsBetween':
            case 'Integer':
            case 'SilieInt':
                $this->type = self::TYPE_INT;
                break;

            // Float / Double
            case 'Numeric':
                $this->type = self::TYPE_DOUBLE;
                break;

            // Range
            case 'Max':
                $this->max = (int)$parameters[0];
                break;
            case 'Min':
                $this->min = (int)$parameters[0];
                break;
            case 'Size':
                $this->min = $this->max = (int)$parameters[0];
                break;
            case 'Between':
                $this->max = (int)$parameters[0];
                $this->min = (int)$parameters[1];
                break;

            // Nullable
            case 'Nullable':
                $this->nullable = true;
                break;

            // Required
            case 'Required':
                $this->required = true;
                break;

            case 'ActiveUrl':
            case 'Bail':
            case 'DateFormat':
            case 'Different':
            case 'Dimensions':
            case 'Distinct':
            case 'Email':
            case 'Exists':
            case 'File':
            case 'Filled':
            case 'Image':
            case 'In':
            case 'InArray':
            case 'Mimetypes':
            case 'Mimes':
            case 'NotIn':
            case 'NotRegex':
            case 'Present':
            case 'Regex':
            case 'RequiredIf':
            case 'RequiredUnless':
            case 'RequiredWith':
            case 'RequiredWithAll':
            case 'RequiredWithout':
            case 'RequiredWithoutAll':
            case 'Same':
            case 'Timezone':
            case 'Unique':
                break;
        }

        $this->primitiveType = $this->primitiveTypeMap[$this->type];
    }
}
