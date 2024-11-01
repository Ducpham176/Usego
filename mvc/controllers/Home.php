<?php 
require_once(dirname(__DIR__)) . "/core/Shared/General.php";

class Home extends General {
    private $access;

    public function __construct() {
        $this->access = new General;
    }

    public function index() {    
        // Get data powerpoints
        $this->view('masterlayout', [
            'page' => 'users/home', 
            'resources' => [
                'title' => 'Usego Chia sẽ các mẫu thiết kế - PowerPoint Miễn Phí',
                'css' => 'home',
                'js' => [
                    'views/home',
                    'core/follow'
                ],
                'swiper' => 'swiper-bundles.min'
            ],
            'dataSql' => $this->getDataSql()
        ]);
    }

    private function getDataSql() {
        return [
            'totalUsers' => $this->totalUsers(),
            'dataPowerpoint' => $this->getFilePowerpoints(),
            'dataTopPowerpoint' => $this->getTopPowerpoints(),
            'dataTopAuthor' => $this->getDataTopAuthor()
        ];
    }

    private function getFilePowerpoints() {
        $userId = $this->access->accessUserId();
        return $this->getRawPowerpoints("LIMIT 11", $userId);
    }

    private function getTopPowerpoints() {
        return $this->getRawPowerpoints("LIMIT 4");
    }

    private function getRawPowerpoints($limit = '', $userId = null) {
        $userIdCondition = $userId ? "AND ug_follow.userId  = '$userId'" : '';
        return $this->access->MyModelsCrud->getRaw("
            SELECT ug_users.id, 
                   ug_users.firstName, ug_users.lastName, ug_users.avatar, ug_users.ug_type, 
                   ug_power_point.*, COUNT(ug_like_post.id) AS like_count,
                   (SELECT COUNT(*) FROM ug_follow WHERE ug_follow.id_onswer = ug_power_point.userId $userIdCondition) as total_follow
            FROM 
                ug_users 
            INNER JOIN 
                ug_power_point ON ug_users.id = ug_power_point.userId
            LEFT JOIN 
                ug_like_post ON ug_power_point.id = ug_like_post.id_onwser
            GROUP BY 
                ug_users.id, ug_power_point.id
            ORDER BY 
                ug_power_point.id DESC $limit
        ");
    }

    private function getTopAuthor() {
        return $this->access->MyModelsCrud->getRaw(" 
            SELECT ug_users.id AS userId, 
                   COUNT(ug_collection_post.id) AS collection_count 
            FROM 
                ug_users 
            INNER JOIN 
                ug_power_point ON ug_users.id = ug_power_point.userId
            LEFT JOIN 
                ug_collection_post ON ug_power_point.id = ug_collection_post.id_onwser
            GROUP BY 
                ug_users.id
            ORDER BY 
                collection_count DESC LIMIT 7
        ");
    }

    private function getDataTopAuthor() {
        $arrayId = $this->getTopAuthor();
        $arrayResult = [];
        foreach ($arrayId as $id) {
            $posts = $this->access->MyModelsCrud->getRaw("
                SELECT ug_users.id, 
                       ug_users.firstName, ug_users.lastName, ug_users.avatar, ug_users.ug_type,
                       ug_power_point.*
                FROM 
                    ug_users, ug_power_point 
                WHERE 
                    ug_power_point.userId = '{$id['userId']}' AND 
                    ug_users.id = '{$id['userId']}'
                ORDER BY 
                    ug_power_point.createAt DESC LIMIT 2 
            ");
            $arrayResult[] = $posts;
        }
        return $arrayResult;
    }

    private function totalUsers() { 
        return $this->access->MyModelsOther->getRows("SELECT id FROM ug_users");
    }
}

