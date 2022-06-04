<?php

declare(strict_types=1);

namespace Example\Model;

use InvalidArgumentException;
use Mini\Controller\Exception\BadInputException;
use Mini\Model\Model;
use UnexpectedValueException;

/**
 * Example data.
 */
class ExampleModel extends Model
{
    public int $id;
    public string $created;
    public string $code;
    public string $description;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get example data by ID.
     *
     * @param int $id example id
     *
     * @return array example data
     */
    public function get(int $id): array
    {
        $sql = '
            SELECT
                example_id AS "id",
                created,
                code,
                description
            FROM
                ' . getenv('DB_SCHEMA') . '.master_example
            WHERE
                example_id = ?';

        $results = $this->db->select([
            'title'  => 'Get example data',
            'sql'    => $sql,
            'inputs' => [$id]
        ]);

        // If the query succeeded, we should get an ID back.
        if (isset($results['id'])) {
            $this->description = $results['description'];
            $this->code = $results['code'];
            $this->created = $results['created'];
            $this->id = $results['id'];
        }

        return $results;
    }

    /**
     * Create an example.
     *
     * @param string $created     example created on
     * @param string $code        example code
     * @param string $description example description
     *
     * @return int example id
     */
    public function create(string $created, string $code, string $description): int
    {
        $sql = '
            INSERT INTO
                ' . getenv('DB_SCHEMA') . '.master_example
            (
                created,
                code,
                description
            )
            VALUES
            (?,?,?)';

        $id = $this->db->statement([
            'title'  => 'Create example',
            'sql'    => $sql,
            'inputs' => [
                $created,
                $code,
                $description
            ]
        ]);

        $this->db->validateAffected();

        // Set values once we know the operation succeeded.
        $this->id = $id;
        $this->code = $code;
        $this->description = $description;
        $this->created = $created;

        return $id;
    }
}
