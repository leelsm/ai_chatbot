<?php
// 보안 설정 파일
// 이 파일은 웹에서 직접 접근할 수 없도록 .htaccess로 보호해야 합니다

// Google Gemini API 설정
define('GEMINI_API_KEY', 'AIzaSyDmnUnmleO67R7Ae9XM06wPpX5Ls9acZGw');
define('GEMINI_API_URL', 'https://generativelanguage.googleapis.com/v1/models/gemini-1.5-flash:generateContent');

// 보안 설정
define('ALLOWED_ORIGINS', ['*']); // 프로덕션에서는 특정 도메인으로 제한
define('MAX_MESSAGE_LENGTH', 500);
define('MAX_CONVERSATION_HISTORY', 20);

// 세션 설정
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 0); // HTTPS가 아닌 경우 0
ini_set('session.use_strict_mode', 1);

// 오류 설정 (프로덕션에서는 false로 설정)
define('DEBUG_MODE', false);
?>
