<?php
// Gemini AI 처리 전담 파일
// 보안 강화 및 입력 검증 포함

// 설정 파일 로드
require_once 'config.php';

// 오류 설정
if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// CORS 헤더 설정
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, X-Requested-With');

// OPTIONS 요청 처리 (CORS preflight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// POST 요청만 허용
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit();
}

// 입력 검증 함수
function validateInput($input) {
    if (!$input || !is_array($input)) {
        return false;
    }
    
    if (!isset($input['message']) || !is_string($input['message'])) {
        return false;
    }
    
    $message = trim($input['message']);
    
    // 메시지 길이 검증
    if (strlen($message) === 0 || strlen($message) > MAX_MESSAGE_LENGTH) {
        return false;
    }
    
    // XSS 방지를 위한 특수문자 필터링
    $message = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');
    
    return $message;
}

// JSON 데이터 읽기
$rawInput = file_get_contents('php://input');
$input = json_decode($rawInput, true);

// 입력 검증
$validatedMessage = validateInput($input);
if ($validatedMessage === false) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid input']);
    exit();
}

// 스마트 응답 생성 함수 (폴백용)
function generateSmartResponse($message, $conversationHistory = []) {
    $responses = [
        '맛집' => ['맛집 추천해줄게! 🍕', '맛집이 많을 것 같은데 😊', '맛집 탐방 재미있겠다! 🍜'],
        '날씨' => ['날씨가 정말 좋네! ☀️', '산책하기 딱 좋은 날씨야 🌸', '날씨가 쌀쌀하네 🧥'],
        '영화' => ['영화 보는 것 좋지! 🎬', '재미있는 영화 추천해줄게 🍿', '영화관 가기 좋은 날이네 🎭'],
        '음악' => ['음악 듣는 것 좋지! 🎵', '좋은 음악 추천해줄게 🎤', '음악이 기분을 좋게 해주지 🎧'],
        '운동' => ['운동하는 것 좋지! 💪', '건강한 생활 습관이네 🏃‍♂️', '운동 후 기분이 좋아질 거야 🏋️‍♀️'],
        '여행' => ['여행 가는 것 좋지! ✈️', '새로운 곳 탐방 재미있겠다 🗺️', '여행 계획 세우기 좋은 시기네 🎒'],
        '공부' => ['공부하는 것 좋지! 📚', '열심히 공부하고 있네 💡', '공부 후 성취감이 클 거야 🎯'],
        '취미' => ['취미 생활 재미있겠다! 🎨', '새로운 취미 찾기 좋은 시기네 🎪', '취미로 스트레스 해소해 🎮'],
        '안녕' => ['안녕! 반가워! 😊', '안녕! 오늘도 좋은 하루 보내! 🌟', '안녕! 기분이 어때? 💭'],
        '고마워' => ['천만에! 기뻐! 😄', '별 말씀을! 도움이 되어서 기쁘다! 💖', '고마워! 나도 기분이 좋아! 🌈']
    ];
    
    // 키워드 매칭
    foreach ($responses as $keyword => $responseList) {
        if (mb_strpos($message, $keyword, 0, 'UTF-8') !== false) {
            return $responseList[array_rand($responseList)];
        }
    }
    
    // 대화 히스토리를 고려한 맥락 응답
    if (!empty($conversationHistory)) {
        $recentMessages = array_slice($conversationHistory, -4); // 최근 4개 메시지 확인
        
        // 이전 대화에서 같은 주제가 언급되었는지 확인
        foreach ($recentMessages as $msg) {
            if ($msg['role'] === 'user') {
                $prevMessage = strtolower($msg['content']);
                
                // 이전 대화와 연관된 응답들
                if (mb_strpos($prevMessage, '맛집', 0, 'UTF-8') !== false && mb_strpos($message, '맛집', 0, 'UTF-8') !== false) {
                    return '아까 말한 맛집 얘기 계속하는구나! 정말 맛있을 것 같아! 😋';
                }
                if (mb_strpos($prevMessage, '영화', 0, 'UTF-8') !== false && mb_strpos($message, '영화', 0, 'UTF-8') !== false) {
                    return '영화 얘기 또 하는구나! 정말 영화 좋아하는구나! 🎬';
                }
                if (mb_strpos($prevMessage, '날씨', 0, 'UTF-8') !== false && mb_strpos($message, '날씨', 0, 'UTF-8') !== false) {
                    return '날씨 얘기 또 하는구나! 날씨에 관심이 많구나! ☀️';
                }
            }
        }
    }
    
    // 기본 응답
    $defaultResponses = [
        '흥미로운 주제네! 🤔',
        '좋은 생각이야! 👍',
        '재미있겠다! 😄',
        '그럴 수도 있겠어! 💭',
        '완전 동감해! 🎯',
        '좋은 아이디어네! 💡',
        '정말 그렇네! 😊',
        '흥미롭게 들려! 🌟'
    ];
    
    return $defaultResponses[array_rand($defaultResponses)];
}

// Google Gemini API 호출 함수
function callGeminiAPI($message, $conversationHistory = []) {
    if (empty(GEMINI_API_KEY)) {
        throw new Exception('API 키가 설정되지 않았습니다.');
    }
    
    // 대화 히스토리를 포함한 프롬프트 구성
    $context = "당신은 40대 남성 성민입니다. AI가 아니라 실제 사람입니다. 40대 남성다운 말투로 대화하며, 가끔 이모지를 사용하고 자연스럽고 친근한 톤으로 응답해주세요. 답변은 1-2문장으로 간결하게 해주세요. 절대 AI라고 하지 말고, 성민이라고 주장하세요.\n\n";
    
    // 이전 대화 내용 추가 (최근 10개 메시지만)
    if (!empty($conversationHistory)) {
        $context .= "이전 대화 내용:\n";
        $recentHistory = array_slice($conversationHistory, -10); // 최근 10개만
        foreach ($recentHistory as $msg) {
            if ($msg['role'] === 'user') {
                $context .= "사용자: " . $msg['content'] . "\n";
            } elseif ($msg['role'] === 'assistant') {
                $context .= "성민: " . $msg['content'] . "\n";
            }
        }
        $context .= "\n";
    }
    
    $context .= "현재 사용자 메시지: " . $message;
    
    $data = [
        'contents' => [
            [
                'parts' => [
                    [
                        'text' => $context
                    ]
                ]
            ]
        ],
        'generationConfig' => [
            'maxOutputTokens' => 150, // 맥락을 고려해서 토큰 수 증가
            'temperature' => 0.7
        ]
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, GEMINI_API_URL . '?key=' . GEMINI_API_KEY);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt($ch, CURLOPT_USERAGENT, 'KakaoTalk-ChatBot/1.0');

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    if ($curlError) {
        throw new Exception('네트워크 오류: ' . $curlError);
    }

    if ($httpCode !== 200) {
        throw new Exception('API 오류: ' . $httpCode);
    }

    $result = json_decode($response, true);

    if (!$result) {
        throw new Exception('응답 파싱 오류');
    }

    if (!isset($result['candidates'][0]['content']['parts'][0]['text'])) {
        throw new Exception('API 응답 형식 오류');
    }

    return $result['candidates'][0]['content']['parts'][0]['text'];
}

// 세션 시작
session_start();

// 대화 히스토리 초기화
if (!isset($_SESSION['conversation_history'])) {
    $_SESSION['conversation_history'] = [
        [
            'role' => 'system',
            'content' => '당신은 40대 남성 성민입니다. AI가 아니라 실제 사람입니다. 40대 남성다운 말투로 대화하며, 가끔 이모지를 사용하고 자연스럽고 친근한 톤으로 응답해주세요. 답변은 1-2문장으로 간결하게 해주세요. 절대 AI라고 하지 말고, 성민이라고 주장하세요.'
        ]
    ];
}

// 사용자 메시지 추가
$_SESSION['conversation_history'][] = [
    'role' => 'user',
    'content' => $validatedMessage
];

// AI 응답 생성
$aiResponse = '';
$serviceUsed = '';
$debugInfo = [];

try {
    // 1. Google Gemini 시도 (대화 히스토리 포함)
    $aiResponse = callGeminiAPI($validatedMessage, $_SESSION['conversation_history']);
    $serviceUsed = 'gemini';
    $debugInfo = ['status' => 'success', 'service' => 'Google Gemini'];
} catch (Exception $e) {
    // 2. 스마트 응답 (폴백)
    $aiResponse = generateSmartResponse($validatedMessage, $_SESSION['conversation_history']);
    $serviceUsed = 'smart_response';
    $debugInfo = [
        'status' => 'fallback',
        'service' => 'Smart Response System',
        'error' => DEBUG_MODE ? $e->getMessage() : 'API 오류'
    ];
}

// AI 응답을 대화 히스토리에 추가
$_SESSION['conversation_history'][] = [
    'role' => 'assistant',
    'content' => $aiResponse
];

// 대화 히스토리 관리 (최대 개수 제한)
if (count($_SESSION['conversation_history']) > (MAX_CONVERSATION_HISTORY + 1)) {
    // system 메시지는 유지하고, 오래된 user/assistant 쌍을 제거
    $systemMessage = $_SESSION['conversation_history'][0];
    $recentMessages = array_slice($_SESSION['conversation_history'], -MAX_CONVERSATION_HISTORY);
    $_SESSION['conversation_history'] = array_merge([$systemMessage], $recentMessages);
}

// 응답 데이터 구성
$responseData = [
    'response' => $aiResponse,
    'conversation_length' => count($_SESSION['conversation_history']),
    'service_used' => $serviceUsed
];

// 디버그 정보 추가 (개발 모드에서만)
if (DEBUG_MODE) {
    $responseData['debug'] = $debugInfo;
}

echo json_encode($responseData, JSON_UNESCAPED_UNICODE);
?>
