<?php
/**
 * Created by Fabrizio Fenoglio.
 * 
 * @package Rally v1.0.0
 * Released under MIT Licence
 * 
 */

namespace Fenos\Rally\Repositories;


use Fenos\Rally\Models\Follower;
use Illuminate\Database\DatabaseManager;

/**
 * Class RallyPolymorphicRepository
 * @package Fenos\Rally\Repositories
 */
class RallyPolymorphicRepository extends RallyRepository implements RallyRepositoryInterface {

    /**
     * @var \Illuminate\Database\DatabaseManager
     */
    protected $db;

    /**
     * @var \Fenos\Rally\Models\Follower
     */
    private $follow;

    /**
     * @param Follower $follow
     * @param DatabaseManager $db
     */
    function __construct(Follower $follow,DatabaseManager $db)
    {
        $this->follow = $follow;
        $this->db = $db;

        parent::__construct($this->follow,$this->db);
    }

    /**
     * @param array $follower
     * @return mixed
     */
    public function isFollower(array $follower)
    {
        return $this->follow->where('follower_type',$follower['follower_type'])
                            ->where('follower_id',  $follower['follower_id'])
                            ->where(function($where) use ($follower){
                                $where->where('followed_type',$follower['followed_type'])
                                ->where('followed_id',$follower['followed_id']);
                            })->first();

    }

    /**
     * @param array $followed
     * @param $filters
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function listsFollowers(array $followed,$filters)
    {
        $lists = $this->follow->with('follower')
                              ->where('followed_type', $followed['follower_type'])
                              ->where('followed_id',$followed['follower_id']);

        $this->addFilters($lists,$filters);

        return $lists->get();
    }

    /**
     * @param array $followed
     * @return mixed
     */
    public function countFollowers(array $followed)
    {
        return $this->follow->select($this->db->raw('Count(*) as numbers_followers'))
            ->where('followed_type', $followed['follower_type'])
            ->where('followed_id',$followed['follower_id'])->first();
    }

}