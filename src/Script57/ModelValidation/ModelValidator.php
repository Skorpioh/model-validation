<?php namespace Script57\ModelValidation;

use Illuminate\Support\Facades\Validator;

abstract class ModelValidator {

    /**
     * The rules to be applied to the data.
     *
     * @var array
     */
    protected static $rules = array();


    /**
     * The array of custom error messages.
     *
     * @var array
     */
    protected static $messages = array();


    /**
     * Whether to throw a ModelValidationException when validation fails, or just return false.
     *
     * @var bool
     */
    protected static $throwException = false;


    /**
     * The model to validate.
     *
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;


    /**
     * The validator instance.
     *
     * @var \Illuminate\Validation\Validator
     */
    protected $validator;


    /**
     * Constructor
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     */
    public function __construct($model = null)
    {
        if ($model)
        {
            // Set model
            $this->model = $model;

            // Bootstrap
            $this->boot();
        }
    }


    /**
     * Build unique exclusion rules
     *
     * @param  array $rules
     * @return array
     */
    protected function buildUniqueExclusionRules(array $rules = array())
    {
        if ( ! count($rules))
        {
            $rules = static::$rules;
        }

        foreach ($rules as $field => &$ruleSet)
        {

            // If $ruleSet is a pipe-separated string, switch it to array
            $ruleSet = is_string($ruleSet) ? explode('|', $ruleSet) : $ruleSet;

            foreach ($ruleSet as &$rule)
            {
                if (strpos($rule, 'unique') === 0)
                {
                    $params = explode(',', $rule);
                    $uniqueRules = array();

                    // Append table name if needed
                    $table = explode(':', $params[0]);
                    $uniqueRules[1] = count($table) == 1
                        ? $this->model->getTable()
                        : $table[1];


                    // Append field name if needed
                    $uniqueRules[2] = count($params) == 1
                        ? $field
                        : $params[1];

                    $uniqueRules[3] = $this->model->getKey();
                    $uniqueRules[4] = $this->model->getKeyName();

                    $rule = 'unique:' . implode(',', $uniqueRules);
                }
            }
        }

        return $rules;
    }


    /**
     * Set up the validator.
     */
    protected function boot()
    {
        // Build unique exclusion rules
        $rules = $this->buildUniqueExclusionRules();

        // Create validator
        $this->validator = Validator::make(
            $this->model->getAttributes(),
            $rules,
            static::$messages
        );
    }


    /**
     * Validate the model.
     *
     * @param null $throwException
     * @return bool
     */
    public function validate($throwException = null)
    {        
        if ($this->validator->passes())
        {
            return true;
        }
        else
        {
            if ($throwException === true || ($throwException !== false && static::$throwException))
            {
                // Throw model validation exception
                throw with(new ModelValidationException)->setErrors($this->validator->errors());
            }

            return false;
        }
    }

    /**
     * Get the validation error messages.
     *
     * @return \Illuminate\Support\MessageBag
     */
    public function getErrors()
    {
        return $this->validator->errors();
    }

    /**
     * Set the model.
     *
     * @param $model
     * @return $this
     */
    public function setModel($model)
    {
        $this->model = $model;

        // Update the validator
        $this->boot();

        return $this;
    }


    /**
     * Get the model.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function getModel()
    {
        return $this->model;
    }


    /**
     * Get the validator instance.
     *
     * @return \Illuminate\Validation\Validator
     */
    public function getValidator()
    {
        return $this->validator;
    }

}
