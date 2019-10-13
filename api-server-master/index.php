<?php
	require './pdos/DatabasePdo.php';
	require './pdos/IndexPdo.php';
	require './pdos/UserPdo.php';
	require './pdos/RedingListPdo.php';
	require './pdos/CommentPdo.php';
	require './pdos/StoryPdo.php';
	require './pdos/ShowPdo.php';
	require './pdos/TopicPdo.php';
	require './pdos/ClapPdo.php';
	require './pdos/SearchPdo.php';
	
	require './vendor/autoload.php'; 

	use \Monolog\Logger as Logger;
	use Monolog\Handler\StreamHandler;
	use Firebase\JWT;

	
	date_default_timezone_set('Asia/Seoul');
	ini_set('default_charset', 'utf8mb4');

	//에러출력하게 하는 코드
	//error_reporting(E_ALL); ini_set("display_errors", 1);

	//Main Server API
	$dispatcher = FastRoute\simpleDispatcher(function (FastRoute\RouteCollector $r) {
		/* ******************   Test   ****************** */
		$r->addRoute('GET', '/', ['IndexController', 'index']);
		$r->addRoute('GET', '/test', ['IndexController', 'test']);
		
		//$r->addRoute('GET', '/test/{testNo}', ['IndexController', 'testDetail']);
		//$r->addRoute('POST', '/test', ['IndexController', 'testPost']);
		$r->addRoute('GET', '/jwt', ['MainController', 'validateJwt']);
		$r->addRoute('POST', '/jwt', ['MainController', 'createJwt']);
		$r->addRoute('GET'		, '/user/story'								, ['ShowController', 'userStory']);
		$r->addRoute('GET'		, '/user/{userId}/story'					, ['ShowController', 'userStory']);		
		$r->addRoute('GET'		, '/search'									, ['SearchController', 'searchType']);
		$r->addRoute('GET'		, '/story/search'							, ['SearchController', 'searchStory']);
		$r->addRoute('GET'		, '/topic/search'							, ['SearchController', 'searchTopic']);
		$r->addRoute('GET'		, '/user/search'							, ['SearchController', 'searchUser']);
		
		$r->addRoute('GET'		, '/user/info'								, ['UserController', 'userList']);
		$r->addRoute('POST'		, '/login'									, ['UserController', 'loginUser']);
		$r->addRoute('POST'		, '/user'									, ['UserController', 'addUser']);
		$r->addRoute('GET'		, '/user'									, ['UserController', 'detailUser']);
		$r->addRoute('DELETE'	, '/user'									, ['UserController', 'deleteUser']);
		$r->addRoute('PATCH'	, '/user'									, ['UserController', 'updataUser']);
		$r->addRoute('GET'		, '/user/{userId}'							, ['UserController', 'detailUser']);

		
		$r->addRoute('POST'		, '/following/{followUserId}'				, ['UserController', 'followUser']);
		$r->addRoute('DELETE'	, '/following/{followUserId}'				, ['UserController', 'deleteFollowUser']);
		$r->addRoute('GET'		, '/follower/{userId}'						, ['UserController', 'followerUser']);
		$r->addRoute('GET'		, '/following/{userId}'						, ['UserController', 'followingUser']);
		
		$r->addRoute('POST'		, '/user/{userId}/block'					, ['UserController', 'blockUser']);
		$r->addRoute('DELETE'	, '/user/{userId}/block'					, ['UserController', 'deleteBlockUser']);
		$r->addRoute('GET'		, '/user/{userId}/block'					, ['UserController', 'blockUserList']);
		
		$r->addRoute('POST'		, '/story'									, ['StoryController', 'addStory']);
		$r->addRoute('POST'		, '/story/{storyId}/contents'				, ['StoryController', 'addContents']);
		$r->addRoute('GET'		, '/story/{storyId}'						, ['StoryController', 'readStory']);
		$r->addRoute('DELETE'	, '/story/{storyId}'						, ['StoryController', 'deleteStory']);
		
		$r->addRoute('GET'		, '/story'									, ['ShowController', 'recentlyStory']);
		$r->addRoute('GET'		, '/recentlylist'							, ['ShowController', 'recentlyList']);
		$r->addRoute('GET'		, '/popularlist'							, ['ShowController', 'popularStory']);

		
		$r->addRoute('POST'		, '/story/{storyId}/clap'					, ['ClapController', 'addClap']);
			
		$r->addRoute('POST'		, '/topic'									, ['TopicController', 'addTopic']);
		$r->addRoute('DELETE'	, '/topic'									, ['TopicController', 'deleteTopic']);
		$r->addRoute('GET'		, '/topic'									, ['TopicController', 'readTopic']);
		$r->addRoute('POST'		, '/topic/{topicId}'						, ['TopicController', 'followTopic']);
		$r->addRoute('DELETE'	, '/topic/{topicId}'						, ['TopicController', 'deleteFollowTopic']);
		
		$r->addRoute('POST'		, '/comment'								, ['CommentController', 'addComment']);
		$r->addRoute('DELETE'	, '/comment/{commentId}'					, ['CommentController', 'deleteComment']);
		$r->addRoute('PATCH'	, '/comment/{commentId}'					, ['CommentController', 'updataComment']);
		$r->addRoute('GET'		, '/story/{storyId}/comment'				, ['CommentController', 'readStoryComment']);
		$r->addRoute('GET'		, '/comment/{commentId}'					, ['CommentController', 'readComment']);
	
	
		$r->addRoute('POST'		, '/readinglist'							, ['RedingListController', 'addReadingList']);
		$r->addRoute('PATCH'	, '/readinglist/{readingListId}'			, ['RedingListController', 'archiveReadingList']);
		$r->addRoute('DELETE'	, '/readinglist/{readingListId}'			, ['RedingListController', 'deleteReadingList']);
		$r->addRoute('GET'		, '/readinglist'							, ['RedingListController', 'readReadingList']);
		
		
	//    $r->addRoute('GET', '/users', 'get_all_users_handler');
	//    // {id} must be a number (\d+)
	//    $r->addRoute('GET', '/user/{id:\d+}', 'get_user_handler');
	//    // The /{title} suffix is optional
	//    $r->addRoute('GET', '/articles/{id:\d+}[/{title}]', 'get_article_handler');
	});

	// Fetch method and URI from somewhere
	$httpMethod = $_SERVER['REQUEST_METHOD'];
	$uri = $_SERVER['REQUEST_URI'];

	// Strip query string (?foo=bar) and decode URI
	if (false !== $pos = strpos($uri, '?')) {
		$uri = substr($uri, 0, $pos);
	}
	$uri = rawurldecode($uri);

	$routeInfo = $dispatcher->dispatch($httpMethod, $uri);

	// 로거 채널 생성
	$accessLogs = new Logger('ACCESS_LOGS');
	$errorLogs = new Logger('ERROR_LOGS');
	// log/your.log 파일에 로그 생성. 로그 레벨은 Info
	$accessLogs->pushHandler(new StreamHandler('logs/access.log', Logger::INFO));
	$errorLogs->pushHandler(new StreamHandler('logs/errors.log', Logger::ERROR));
	// add records to the log
	//$log->addInfo('Info log');
	// Debug 는 Info 레벨보다 낮으므로 아래 로그는 출력되지 않음
	//$log->addDebug('Debug log');
	//$log->addError('Error log');

	switch ($routeInfo[0]) {
		case FastRoute\Dispatcher::NOT_FOUND:
			// ... 404 Not Found
			echo "404 Not Found";
			break;
		case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
			$allowedMethods = $routeInfo[1];
			// ... 405 Method Not Allowed
			echo "405 Method Not Allowed";
			break;
		case FastRoute\Dispatcher::FOUND:
			$handler = $routeInfo[1];
			$vars = $routeInfo[2];

			switch ($routeInfo[1][0]) {
				case 'IndexController':
					$handler = $routeInfo[1][1];
					$vars = $routeInfo[2];
					require './controllers/IndexController.php';
					break;
				case 'MainController':
					$handler = $routeInfo[1][1];
					$vars = $routeInfo[2];
					require './controllers/MainController.php';
					break;
				case 'UserController':
					$handler = $routeInfo[1][1];
					$vars = $routeInfo[2];
					require './controllers/UserController.php';
					break;
				case 'RedingListController':
					$handler = $routeInfo[1][1];
					$vars = $routeInfo[2];
					require './controllers/RedingListController.php';
					break;	
				case 'CommentController':
					$handler = $routeInfo[1][1];
					$vars = $routeInfo[2];
					require './controllers/CommentController.php';
					break;
				case 'StoryController':
					$handler = $routeInfo[1][1];
					$vars = $routeInfo[2];
					require './controllers/StoryController.php';
					break;
				case 'ShowController':
					$handler = $routeInfo[1][1];
					$vars = $routeInfo[2];
					require './controllers/ShowController.php';
					break;
				case 'TopicController':
					$handler = $routeInfo[1][1];
					$vars = $routeInfo[2];
					require './controllers/TopicController.php';
					break;
				case 'ClapController':
					$handler = $routeInfo[1][1];
					$vars = $routeInfo[2];
					require './controllers/ClapController.php';
					break;
				case 'SearchController':
					$handler = $routeInfo[1][1];
					$vars = $routeInfo[2];
					require './controllers/SearchController.php';
					break;
				/*case 'EventController':
					$handler = $routeInfo[1][1]; $vars = $routeInfo[2];
					require './controllers/EventController.php';
					break;
				case 'ProductController':
					$handler = $routeInfo[1][1]; $vars = $routeInfo[2];
					require './controllers/ProductController.php';
					break;
				case 'SearchController':
					$handler = $routeInfo[1][1]; $vars = $routeInfo[2];
					require './controllers/SearchController.php';
					break;
				case 'ReviewController':
					$handler = $routeInfo[1][1]; $vars = $routeInfo[2];
					require './controllers/ReviewController.php';
					break;
				case 'ElementController':
					$handler = $routeInfo[1][1]; $vars = $routeInfo[2];
					require './controllers/ElementController.php';
					break;
				case 'AskFAQController':
					$handler = $routeInfo[1][1]; $vars = $routeInfo[2];
					require './controllers/AskFAQController.php';
					break;*/
			}

			break;
	}
