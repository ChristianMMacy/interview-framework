<?php

declare(strict_types = 1);

namespace Example\View;

use Example\Model\ExampleModel;
use Mini\Controller\Exception\BadInputException;

/**
 * Example view builder.
 */
class ExampleView
{
    /**
     * Setup.
     */
    public function __construct()
    {
    }

    /**
     * Get the example view to display its data.
     *
     * @return string view template
     *
     * @throws BadInputException if no example data is returned
     */
    public function get(ExampleModel $model): string
    {
        $model->hydrate($model->id);

        $data =  array(
            'created'     => $model->created,
            'code'        => $model->code,
            'description' => $model->description,
        );

        return view('app/example/detail', $data);
    }
}
