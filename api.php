<?php
// 오류 표시 설정 (디버깅용)
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

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

// JSON 데이터 읽기
$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !isset($input['message'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Message is required']);
    exit();
}

// Google Gemini API 설정 (무료)
$geminiApiKey = 'AIzaSyDmnUnmleO67R7Ae9XM06wPpX5Ls9acZGw';
$geminiApiUrl = 'https://generativelanguage.googleapis.com/v1/models/gemini-1.5-flash:generateContent';

// 스마트 응답 생성 함수 (최종 폴백)
function generateSmartResponse($message) {
    $responses = [
        '누구' => [
            '이성민이야. 반가워.',
            '성민이라고 해.',
            '이성민이야. 40대 남성이고 가족이 있어.',
            '성민이야. 2016년에 결혼했고 아이들도 있어.'
        ],
        'AI' => [
            '그냥 성민이야.',
            '평범한 사람이야.',
            'AI가 아니라 사람이야.',
            '그냥 보통 사람이야.'
        ],
        '맛집' => [
            '맛집 얘기하는구나! 좋은 맛집 많을 것 같은데.',
            '맛집 탐방 재미있겠다!',
            '맛있는 음식 먹고 기분 좋아질 거야.',
            '맛집 추천해줄까?',
            '맛집 찾기 재미있지!',
            '맛집 얘기하니까 배고프네.'
        ],
        '날씨' => [
            '날씨가 정말 좋네!',
            '산책하기 딱 좋은 날씨야.',
            '날씨가 쌀쌀하네.',
            '오늘 날씨로 기분이 좋아질 것 같아!',
            '날씨 얘기하니까 밖에 나가고 싶네.',
            '날씨가 참 좋은 것 같아.'
        ],
        '영화' => [
            '영화 보는 것 좋지!',
            '재미있는 영화 추천해줄까?',
            '영화관 가기 좋은 날이네.',
            '영화로 스트레스 해소해!',
            '영화 얘기하니까 보고 싶네.',
            '좋은 영화 많을 것 같은데.'
        ],
        '음악' => [
            '음악 듣는 것 좋지!',
            '좋은 음악 추천해줄까?',
            '음악이 기분을 좋게 해주지.',
            '음악으로 마음이 편해질 거야!',
            '음악 얘기하니까 듣고 싶네.',
            '좋은 음악 많을 것 같은데.'
        ],
        '운동' => [
            '운동하는 것 좋지!',
            '건강한 생활 습관이네.',
            '운동 후 기분이 좋아질 거야.',
            '운동으로 활력이 생길 거야!',
            '운동 얘기하니까 하고 싶네.',
            '건강한 생활이 중요하지.'
        ],
        '여행' => [
            '여행 가는 것 좋지!',
            '새로운 곳 탐방 재미있겠다.',
            '여행 계획 세우기 좋은 시기네.',
            '여행으로 좋은 추억 만들자!',
            '여행 얘기하니까 가고 싶네.',
            '새로운 곳 가는 것 재미있지.'
        ],
        '공부' => [
            '공부하는 것 좋지!',
            '열심히 공부하고 있네.',
            '공부 후 성취감이 클 거야.',
            '공부로 실력이 늘어날 거야!',
            '공부 얘기하니까 열심히 해야겠네.',
            '학습하는 것 중요하지.'
        ],
        '취미' => [
            '취미 생활 재미있겠다!',
            '새로운 취미 찾기 좋은 시기네.',
            '취미로 스트레스 해소해.',
            '취미로 일상이 즐거워질 거야!',
            '취미 얘기하니까 하고 싶네.',
            '취미 생활이 중요하지.'
        ],
        '안녕' => [
            '안녕! 반가워!',
            '안녕! 오늘도 좋은 하루 보내!',
            '안녕! 기분이 어때?',
            '안녕! 잘 지내?',
            '안녕! 오늘도 힘내!',
            '안녕! 반가워!'
        ],
        '고마워' => [
            '천만에! 기뻐!',
            '별 말씀을! 도움이 되어서 기쁘다!',
            '고마워! 나도 기분이 좋아!',
            '천만에! 언제든지 말해!',
            '별 말씀을! 기뻐!',
            '고마워! 도움이 되어서 기쁘다!'
        ],
        '피곤' => [
            '피곤하시는군요! 푹 쉬세요!',
            '피곤할 때는 잠시 쉬는 것도 좋아요!',
            '피곤함을 이겨내세요! 응원할게요!',
            '피곤하시면 푹 쉬세요.',
            '피곤할 때는 잠시 쉬는 게 좋아.',
            '힘내! 곧 좋아질 거야.'
        ],
        '스트레스' => [
            '스트레스 받으시는군요! 힘내세요!',
            '스트레스 해소하는 방법을 찾아보세요!',
            '스트레스는 잠시뿐이에요! 곧 좋아질 거예요!',
            '스트레스 받으면 힘들지.',
            '스트레스 해소하는 게 중요해.',
            '힘내! 곧 좋아질 거야.'
        ],
        '기쁘' => [
            '기뻐하시는군요! 저도 기뻐요!',
            '기쁜 일이 있으셨나요? 축하해요!',
            '기쁜 마음이 전해져요!',
            '기뻐하시는구나! 축하해!',
            '기쁜 일이 있으셨군요!',
            '기뻐하시는 모습이 보기 좋아.'
        ],
        '슬프' => [
            '슬프시군요! 위로해드릴게요!',
            '슬픈 일이 있으셨나요? 곧 좋아질 거예요!',
            '슬픔은 잠시뿐이에요! 힘내세요!',
            '슬프시는구나. 위로해드릴게.',
            '슬픈 일이 있으셨군요.',
            '힘내! 곧 좋아질 거야.'
        ],
        '화나' => [
            '화가 나시는군요! 진정하세요!',
            '화가 날 때는 심호흡을 해보세요!',
            '화는 곧 가라앉을 거예요! 차분히 생각해보세요!',
            '화가 나시는구나. 진정해.',
            '화가 날 때는 심호흡을 해봐.',
            '차분히 생각해보자.'
        ],
        '일' => [
            '일 얘기하는구나! 열심히 하고 있네.',
            '일이 잘 되고 있나?',
            '일 얘기하니까 열심히 해야겠네.',
            '일이 힘들 때도 있지.',
            '일 얘기하니까 신경 쓰이네.',
            '일도 중요하지만 휴식도 필요해.'
        ],
        '가족' => [
            '가족 얘기하는구나! 소중하지.',
            '가족이 있으니까 든든하지.',
            '가족 얘기하니까 따뜻하네.',
            '가족이 제일 중요하지.',
            '가족 얘기하니까 기분이 좋아.',
            '가족과 함께하는 시간이 소중해.'
        ],
        '친구' => [
            '친구 얘기하는구나! 좋은 친구가 있네.',
            '친구와 함께하는 시간이 즐거우지.',
            '친구 얘기하니까 기분이 좋아.',
            '친구가 있으니까 든든하지.',
            '친구와의 우정이 소중해.',
            '친구 얘기하니까 만나고 싶네.'
        ],
        '시간' => [
            '시간 얘기하는구나! 소중하지.',
            '시간이 참 빠르게 지나가네.',
            '시간 얘기하니까 신중하게 써야겠네.',
            '시간이 참 귀하지.',
            '시간 얘기하니까 아깝네.',
            '시간을 잘 활용하는 게 중요해.'
        ],
        '미래' => [
            '미래 얘기하는구나! 희망적이네.',
            '미래가 기대되지?',
            '미래 얘기하니까 설레네.',
            '미래를 준비하는 게 중요해.',
            '미래 얘기하니까 꿈꾸고 싶네.',
            '미래가 밝을 거야.'
        ]
    ];
    
    // 키워드 매칭 (더 정확한 매칭)
    foreach ($responses as $keyword => $responseList) {
        if (mb_strpos($message, $keyword, 0, 'UTF-8') !== false) {
            return $responseList[array_rand($responseList)];
        }
    }
    
    // 기본 응답 (더 다양하게)
    $defaultResponses = [
        '흥미로운 주제네!',
        '좋은 생각이야!',
        '재미있겠다!',
        '그럴 수도 있겠어!',
        '완전 동감해!',
        '좋은 아이디어네!',
        '정말 그렇네!',
        '흥미롭게 들려!',
        '재미있는 대화네!',
        '좋은 관점이야!',
        '흥미로운 생각이야!',
        '재미있게 들려!',
        '좋은 주제네!',
        '흥미롭게 생각해보자!',
        '재미있는 아이디어야!',
        '그런 생각도 있구나!',
        '흥미롭게 들려!',
        '좋은 얘기네!',
        '재미있겠다!',
        '그럴 수도 있겠어!',
        '완전 동감해!',
        '좋은 생각이야!',
        '정말 그렇네!',
        '흥미롭게 들려!',
        '재미있는 대화야!',
        '좋은 관점이야!',
        '흥미로운 생각이야!',
        '재미있게 들려!',
        '좋은 주제야!',
        '흥미롭게 생각해보자!',
        '재미있는 아이디어야!'
    ];
    
    return $defaultResponses[array_rand($defaultResponses)];
}

// Google Gemini API 호출 함수
function callGeminiAPI($message) {
    global $geminiApiKey, $geminiApiUrl;
    
    if (empty($geminiApiKey)) {
        throw new Exception('Gemini API 키가 설정되지 않았습니다.');
    }
    
    $data = [
        'contents' => [
            [
                'parts' => [
                    [
                        'text' => "당신은 40대 남성 이성민입니다. AI가 아니라 실제 사람입니다. 2016년에 결혼했고 2명의 아이가 있습니다. 40대 남성다운 말투로 대화하며, 자연스럽고 친근한 톤으로 응답해주세요. 답변은 1-2문장으로 간결하게 해주세요. 절대 AI라고 하지 말고, 성민이라고 주장하세요.\n\n사용자 메시지: " . $message
                    ]
                ]
            ]
        ],
        'generationConfig' => [
            'maxOutputTokens' => 100,
            'temperature' => 0.7
        ]
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $geminiApiUrl . '?key=' . $geminiApiKey);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    if ($curlError) {
        throw new Exception('Gemini CURL 오류: ' . $curlError);
    }

    if ($httpCode !== 200) {
        throw new Exception('Gemini HTTP 오류: ' . $httpCode . ' - 응답: ' . $response);
    }

    $result = json_decode($response, true);

    if (!$result) {
        throw new Exception('Gemini JSON 파싱 오류: ' . $response);
    }

    if (!isset($result['candidates'][0]['content']['parts'][0]['text'])) {
        throw new Exception('Gemini API 응답 형식 오류: ' . $response);
    }

    return $result['candidates'][0]['content']['parts'][0]['text'];
}

// 대화 히스토리 (세션 기반으로 관리)
session_start();
if (!isset($_SESSION['conversation_history'])) {
    $_SESSION['conversation_history'] = [
        [
            'role' => 'system',
            'content' => '당신은 40대 남성 이성민입니다. AI가 아니라 실제 사람입니다. 2016년에 결혼했고 2명의 아이가 있습니다. 40대 남성다운 말투로 대화하며, 자연스럽고 친근한 톤으로 응답해주세요. 답변은 1-2문장으로 간결하게 해주세요. 절대 AI라고 하지 말고, 성민이라고 주장하세요.'
        ]
    ];
}

// 사용자 메시지 추가
$_SESSION['conversation_history'][] = [
    'role' => 'user',
    'content' => $input['message']
];

// Gemini + 스마트 응답 폴백 시스템
$aiResponse = '';
$debugInfo = [];
$serviceUsed = '';

// 1. Google Gemini 시도
try {
    $aiResponse = callGeminiAPI($input['message']);
    $serviceUsed = 'gemini';
    $debugInfo = ['status' => 'success', 'service' => 'Google Gemini'];
} catch (Exception $e) {
    $debugInfo['gemini_error'] = $e->getMessage();
    
    // 2. 스마트 응답 (폴백)
    $aiResponse = generateSmartResponse($input['message']);
    $serviceUsed = 'smart_response';
    $debugInfo = [
        'status' => 'fallback',
        'service' => 'Smart Response System',
        'gemini_error' => $e->getMessage()
    ];
}

// AI 응답을 대화 히스토리에 추가
$_SESSION['conversation_history'][] = [
    'role' => 'assistant',
    'content' => $aiResponse
];

// 대화 히스토리가 너무 길어지면 오래된 메시지 제거 (최대 20개 메시지 유지)
if (count($_SESSION['conversation_history']) > 21) { // system + 20 messages
    array_splice($_SESSION['conversation_history'], 1, 2); // system 메시지는 유지하고 user/assistant 쌍 제거
}

$responseData = [
    'response' => $aiResponse,
    'conversation_length' => count($_SESSION['conversation_history']),
    'service_used' => $serviceUsed
];

// 디버깅 정보 추가
$responseData['debug'] = $debugInfo;

echo json_encode($responseData);
?>
