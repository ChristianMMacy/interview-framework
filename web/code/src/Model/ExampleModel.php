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
    public ?string $code;
    public ?string $description;
    public string $created;

    /**
     * CTOR for an Example record.
     *
     * @param string|null $code        (Optional) Example code.
     * @param string|null $description (Optional) Example description.
     * @param string|null $created     (Optional) Time and date. Defaults to now.
     */
    public function __construct(string $code = null, string $description = null,
        string $created = null
    )
    {
        parent::__construct();
        $this->code = $code;
        $this->description = $description;
        $this->created = $created ?? now();
    }

    /**
     * Hydrate example data by ID.
     *
     * @throws BadInputException
     */
    public function hydrate(int $id)
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
        } else {
            throw new BadInputException('Unknown example ID');
        }
    }

    /**
     * Create an example.
     *
     */
    public function create()
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
                $this->created,
                $this->code,
                $this->description
            ]
        ]);

        $this->db->validateAffected();

        // Set ID once we know the operation succeeded.
        $this->id = $id;
    }
}
