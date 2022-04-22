<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use App\Models\AAATrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class User
 * 
 * @property int|null $id
 * @property string|null $name
 * @property string $password
 * @property string $remember_token
 * @property string $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models\Base
 */
class User extends Model
{
	use AAATrait;
	protected $table = 'users';

	protected $fillable = [
		'name',
		'password',
		'remember_token'
	];
}
