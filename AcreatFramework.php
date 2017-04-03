<?
// Déclarations
if (!defined('FRAMEWORK_DIR')) define ('FRAMEWORK_DIR', dirname(__FILE__));
define('DS'			, DIRECTORY_SEPARATOR);

// Chargement des librairies standarts
require_once FRAMEWORK_DIR.DS."GlobalFunctions.php";
require_once FRAMEWORK_DIR.DS.'config'.DS.'paths.php';
require_once FRAMEWORK_DIR.DS."AcreatDispatcher.php";
require_once FRAMEWORK_DIR.DS."libs".DS."controller".DS."AcreatController.php";
require_once FRAMEWORK_DIR.DS."libs".DS."view".DS."AcreatView.php";
require_once FRAMEWORK_DIR.DS."libs".DS."model".DS."AcreatModel.php";

ini_set("include_path", ini_get("include_path").":".LIBS.":".VENDORS);

if(file_exists(APP . "globals.php"))
	require_once APP . "globals.php";

// Chargement de la classe de Controller principale
require_once( file_exists( APP . "app_controller.php") ? APP . "app_controller.php" : LIBS."controller".DS."app_controller.php" );

// Chargement des controllers publiques
if( $controllers = glob( LIBS . "controller".DS."globals".DS."*Controller.php") ) {
foreach( $controllers as $controller)
	require_once( $controller );
}

// Chargement des controllers privées
if( $controllers = glob( CONTROLLERS . "*.php") ) {
foreach( $controllers as $controller)
	require_once( $controller );
}

// Chargement de la classe de Model principale
require_once( file_exists( APP . "app_model.php") ? APP . "app_model.php" : LIBS."model".DS."app_model.php" );

// Chargement de la classe de View principale
require_once( file_exists( APP . "app_view.php") ? APP . "app_view.php" : LIBS."view".DS."app_view.php" );


// Chargement des modeles privées
if( $models = glob( MODELS . "*.php") ) {
	foreach( $models as $model)
		require_once( $model );
}

// Chargement des repositories privees
if (defined('REPOSITORIES')) {
    if( $repositories = glob( REPOSITORIES . "*.php") ) {
        foreach( $repositories as $repository) {
            require_once( $repository );
        }
    }
}

// Chargement des repositories privees
if (defined('SERVICES')) {
    if( $services = glob( SERVICES . "*.php") ) {
        foreach( $services as $service) {
            require_once( $service );
        }
    }
}

class AcreatFramework
{
	var $dispatcher;
	// ---
	function AcreatFramework()
	{
		session_start();
		$_SESSION['start_time'] = microtime(true);
		$this->dispatcher = new AcreatDispatcher();
	}
}
?>
