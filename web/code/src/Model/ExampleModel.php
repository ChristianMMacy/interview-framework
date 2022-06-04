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

    /**
     * CTOR for an Example record.
     *
     * @param int|null    $id          (Optional) If provided, retrieves an existing example.
     * @param string|null $code        (Optional if `$id` provided) Example code.
     * @param string|null $description (Optional if `$id` provided) Example description.
     * @param string|null $created     (Optional) Time and date. Defaults to now.
     * @throws BadInputException
     */
    public function __construct(int $id = null, string $code = null, string $description = null,
        string $created = null
    )
    {
        parent::__construct();
        if ($id) {
            $this->get($id);
        } else {
            $this->create($code, $description, $created ?? now());
        }
    }

    /**
     * Get example data by ID.
     *
     * @param int $id example id
     * @throws BadInputException
     */
    private function get(int $id)
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
        } else {
            throw new BadInputException('Unknown example ID');
        }
    }

    /**
     * Create an example.
     *
     * @param string $code        example code
     * @param string $description example description
     * @param string $created     example created on
     */
    private function create(string $code, string $description, string $created)
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
    }
}
