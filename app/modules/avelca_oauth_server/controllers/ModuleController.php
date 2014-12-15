<?php namespace App\Modules\Avelca_Oauth_Server\Controllers;

use Schema;
use DB;
use App\Modules\Avelca_Module\Controllers\ToolController;

class ModuleController extends \BaseController {

	public static function install()
	{
		$message = ToolController::artisan('migrate --package="lucadegasperi/oauth2-server-laravel"');
		$message .= ToolController::artisan('module:migrate avelca_oauth_server');
		$message .= ToolController::artisan('module:seed avelca_oauth_server');
		return $message;
	}

	public function uninstall()
	{
		$tables = array(
			'oauth_clients',
			'oauth_client_endpoints',
			'oauth_sessions',
			'oauth_session_access_tokens',
			'oauth_session_authcodes',
			'oauth_session_redirects',
			'oauth_session_refresh_tokens',
			'oauth_scopes',
			'oauth_session_token_scopes',
			'oauth_session_authcode_scopes',
			'oauth_grants',
			'oauth_client_grants',
			'oauth_client_scopes',
			'oauth_grant_scopes',
			'oauth_client_metadata'
			);

		ToolController::deleteTables($tables);

		$migrations = array(
			'2013_07_24_132419_create_oauth_clients_table',
			'2013_07_24_133032_create_oauth_client_endpoints_table',
			'2013_07_24_133359_create_oauth_sessions_table',
			'2013_07_24_133833_create_oauth_session_access_tokens_table',
			'2013_07_24_134209_create_oauth_session_authcodes_table',
			'2013_07_24_134437_create_oauth_session_redirects_table',
			'2013_07_24_134700_create_oauth_session_refresh_tokens_table',
			'2013_07_24_135036_create_oauth_scopes_table',
			'2013_07_24_135250_create_oauth_session_token_scopes_table',
			'2013_07_24_135634_create_oauth_session_authcode_scopes_table',
			'2013_08_07_112010_create_oauth_grants_table',
			'2013_08_07_112252_create_oauth_client_grants_table',
			'2013_08_07_183251_create_oauth_client_scopes_table',
			'2013_08_07_183635_create_oauth_grant_scopes_table',
			'2013_08_07_183636_create_oauth_client_metadata_table'
			);

		ToolController::deleteMigrations($migrations);

		$permissions = array(
			'api.client',
			'api.client.create',
			'api.client.edit',
			'api.client.delete',
			'api.scope',
			'api.scope.create',
			'api.scope.edit',
			'api.scope.delete'
			);

		ToolController::deletePermissions($permissions);
	}
}

