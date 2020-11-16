<?php namespace Simpus\Helper;

use System\Database\MyPDO;

class Scheduler
{
    /** @var MyPDO */
    private $db;
    private $id = '';
    private $project_name;
    private $last;
    private $interval;
    private $looping;

    // getter
    public function getProjectName(): string
    {
        return $this->project_name;
    }

    public function getLastModife(): int
    {
        return (int) $this->last;
    }

    public function getInterval(): int
    {
        return (int) $this->interval;
    }

    public function getLooping(): string
    {
        return $this->looping;
    }

    // setter
    public function setProjectName(string $val)
    {
        $this->project_name = $val;
        return $this;
    }

    public function setLastModife(int $val)
    {
        $this->last = $val;
        return $this;
    }

    public function setInterval(int $val)
    {
        $this->interval = $val;
        return $this;
    }

    public function setLooping(int $val)
    {
        $this->looping = $val;
        return $this;
    }

    public function __construct(string $id = '')
    {
        $this->db = new MyPDO();
        $this->id = $id;
    }

    public function create(): bool
    {
        if ($this->id != '') return false;
        
        $this->db->query("INSERT INTO `task_secheduler`
                            (
                                `id`, `project_name`, `last`, `interval`, `looping`
                            )
                            VALUE (
                                :id, :project_name, :last, :interval, :looping
                            )");
        $this->db->bind(':id', '');
        $this->db->bind(':project_name', $this->project_name);
        $this->db->bind(':last', $this->last);
        $this->db->bind(':interval', $this->interval);
        $this->db->bind(':looping', $this->looping);

        $this->db->execute();
        if ($this->db->rowCount() > 0) return true;

        return false;
    }

    public function read(): bool
    {
        if ($this->id == '') return false;
        $this->db->query(
            "SELECT *
                FROM `task_secheduler`
                WHERE `id` = :id
            ");
        $this->db->bind(':id', $this->id);
        $row = $this->db->single();
        if ($row) {
            $this->project_name = $row['project_name'];
            $this->last         = $row['last'];
            $this->interval     = $row['interval'];
            $this->looping      = $row['looping'];

            return true;
        }
        return false;
    }

    public function update()
    {
        if ($this->id == '') return false;
        $this->db->query(
            "UPDATE `task_secheduler`
                SET
                    `project_name` = :project_name,
                    `last`  = :last,
                    `interval`     = :interval,
                    `looping`      = :looping
                WHERE
                    `id` = :id
            ");
        $this->db->bind(':project_name', $this->project_name);
        $this->db->bind(':last', $this->last);
        $this->db->bind(':interval', $this->interval);
        $this->db->bind(':looping', $this->looping);
        $this->db->bind(':id', $this->id);
        
        $this->db->execute();
        if( $this->db->rowCount() > 0) return true;

        return false;
    }

}
