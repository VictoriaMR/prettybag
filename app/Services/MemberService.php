<?php 

namespace app\Services;

use App\Services\Base as BaseService;
use App\Models\Member;

/**
 * 	用户公共类
 */
class MemberService extends BaseService
{	
	protected static $constantMap = [
        'base' => Member::class,
    ];

	public function __construct(Member $model)
    {
        $this->baseModel = $model;
    }

	public function create($data)
	{
		if (empty($data['password'])) return false;
		$data['salt'] = $this->getSalt();
		$data['password'] = password_hash($this->getPasswd($data['password'], $data['salt']), PASSWORD_DEFAULT);

		return $this->baseModel->insertGetId($data);
	}

	public function updateById($memId, $data)
	{
		if (empty($memId) || empty($data)) return false;

		if (!empty($data['password'])) {
			$data['salt'] = $this->getSalt();
			$data['password'] = password_hash($this->getPasswd($data['password'], $data['salt']), PASSWORD_DEFAULT);
		}
		$result = $this->baseModel->updateDataById($memId, $data);
		if ($result) {
			$this->clearCache($memId);
		}
		return $result;
	}

	public function login($mobile, $password, $type = 1)
	{
		if (empty($mobile) || empty($password)) return false;

		$info = $this->getInfoByMobile($mobile);
		if (empty($info)) return false;
		if (!$info['status']) return false;

		if ($this->checkPassword($this->getPasswd($password, $info['salt']), $info['password'])) {
			$data = [
				'mem_id' => $info['mem_id'],
				'name' => $info['name'],
				'mobile' => $info['mobile'],
				'nickname' => $info['nickname'],
				'avatar' => $info['avatar'],
				'salt' => $info['salt'],
			];
			switch ($type) {
				case 1:
					$key = 'home';
					break;
				case 3:
					$key = 'proxy';
					break;
				case 5:
					$key = 'admin';
					break;
			}
			return \frame\Session::set($key, $data);
		}
		return false;
	}

	public function checkPassword($inPassword = '', $sourcePassword = '')
	{
		return password_verify($inPassword, $sourcePassword);
	}

	public function isExistUserByMobile($mobile) 
	{
		return $this->baseModel->isExistUserByMobile($mobile);
	}

	public function getInfoByMobile($mobile)
	{
		return $this->baseModel->getInfoByMobile($mobile);
	}

    public function getInfo($userId)
    {
        $info = $this->baseModel->getInfo($userId);
    	if (!empty($info)) {
        	if (empty($info['avatar'])) {
        		$info['avatar'] = $this->getDefaultAvatar($info['mem_id']);
        	} else {
        		$info['avatar'] = mediaUrl($info['avatar']);
        	}
        }
        return $info;
    }

    public function getInfoCache($userId)
    {
        $cacheKey = $this->getInfoCacheKey($userId);
        $info = redis()->get($cacheKey);
        if (empty($info)) {
            $info = $this->getInfo($userId);
            redis()->set($cacheKey, $info, self::constant('INFO_CACHE_TIMEOUT'));
        }
        return $info;
    }

    public function getDefaultAvatar($userId, $male = true)
    {
    	if ($male) {
    		return siteUrl('image/Common/male.jpg');
    	} else {
    		return siteUrl('image/Common/female.jpg');
    	}
    }

    public function getInfoCacheKey($userId)
    {
        return 'MEMBER_INFO_CACHE_' . $userId;
    }

    public function clearCache($userId)
    {
        return redis()->foget($this->getInfoCacheKey($userId));
    }
}