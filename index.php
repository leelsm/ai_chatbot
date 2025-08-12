<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>카카오톡 채팅방</title>
    <meta property="og:title" content="카카오톡 채팅방" />
    <meta property="og:description" content="이성민과의 카카오톡 채팅방" />
    <meta property="og:image" content="sungmin.jpg" />
    <meta property="og:type" content="website" />
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="카카오톡 채팅방" />
    <meta name="twitter:description" content="이성민과의 카카오톡 채팅방" />
    <meta name="twitter:image" content="sungmin.jpg" />
    <link rel="icon" type="image/png" href="kakao.png" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        // CDN 경고 숨기기
        console.warn = function() {};
        
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'kakao-yellow': '#FEE500',
                        'kakao-gray': '#F2F3F5',
                        'kakao-dark': '#1E1E1E',
                        'kakao-light-gray': '#F8F9FA',
                        'kakao-border': '#E5E5E5'
                    }
                }
            }
        }
    </script>
    <style>
        .chat-bubble {
            max-width: 70%;
            word-wrap: break-word;
        }
        .chat-bubble.sent {
            background: linear-gradient(135deg, #FEE500 0%, #FFD700 100%);
        }
        .chat-bubble.received {
            background: white;
        }
        .typing-indicator {
            animation: typing 1.4s infinite;
        }
        @keyframes typing {
            0%, 60%, 100% { transform: translateY(0); }
            30% { transform: translateY(-10px); }
        }
    </style>
</head>
<body class="bg-gray-100 h-screen">
    <!-- 헤더 -->
    <div class="bg-white border-b border-gray-200 px-4 py-3 flex items-center justify-between shadow-sm">
        <div class="flex items-center space-x-3">
            <button class="text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </button>
                         <div class="flex items-center space-x-2">
                 <img src="sungmin.jpg" alt="프로필" class="w-10 h-10 rounded-full">
                                  <div>
                      <h1 class="font-semibold text-gray-900">이성민</h1>
                      <p class="text-xs text-gray-500" id="activity-status">지금 활동중</p>
                  </div>
             </div>
        </div>
        <div class="flex items-center space-x-4">
            <button class="text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </button>
            <button class="text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path>
                </svg>
            </button>
        </div>
    </div>

    <!-- 채팅 영역 -->
    <div class="flex-1 overflow-y-auto px-4 py-4 space-y-4 bg-gray-50" style="height: calc(100vh - 140px);">
        <!-- 오늘 날짜 표시 -->
        <div class="text-center">
            <span class="text-xs text-gray-500" id="current-date"></span>
        </div>
        <!-- 빈 채팅 영역 - 메시지가 여기에 동적으로 추가됩니다 -->
    </div>

    <!-- 입력 영역 -->
    <div class="bg-white border-t border-gray-200 px-4 py-3">
        <div class="flex items-center space-x-3">
            <button class="text-gray-500 hover:text-gray-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                </svg>
            </button>
            <div class="flex-1 bg-gray-100 rounded-full px-4 py-2 flex items-center">
                <input type="text" placeholder="메시지를 입력하세요..." class="flex-1 bg-transparent outline-none text-gray-800 placeholder-gray-500">
                <button class="text-gray-500 hover:text-gray-700 ml-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </button>
            </div>
            <button class="bg-kakao-yellow text-gray-800 rounded-full p-2 hover:bg-yellow-400 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                </svg>
            </button>
        </div>
    </div>

    <script>
        // 채팅 영역과 입력 필드 요소들
        const chatArea = document.querySelector('.flex-1.overflow-y-auto');
        const messageInput = document.querySelector('input[type="text"]');
        const sendButton = document.querySelector('.bg-kakao-yellow');
        
        // API 설정
        const API_URL = 'api.php';

        // 현재 시간을 가져오는 함수
        function getCurrentTime() {
            const now = new Date();
            const hours = now.getHours();
            const minutes = now.getMinutes();
            const period = hours >= 12 ? '오후' : '오전';
            const displayHours = hours > 12 ? hours - 12 : hours;
            return `${period} ${displayHours}:${minutes.toString().padStart(2, '0')}`;
        }

        // 오늘 날짜를 가져오는 함수
        function getCurrentDate() {
            const now = new Date();
            const year = now.getFullYear();
            const month = now.getMonth() + 1;
            const day = now.getDate();
            const dayOfWeek = ['일', '월', '화', '수', '목', '금', '토'][now.getDay()];
            return `${year}년 ${month}월 ${day}일 (${dayOfWeek})`;
        }

        // 메시지 추가 함수
        function addMessage(message, isSent = true) {
            const messageDiv = document.createElement('div');
            const time = getCurrentTime();
            
            if (isSent) {
                // 보낸 메시지
                messageDiv.className = 'flex items-end justify-end space-x-2';
                messageDiv.innerHTML = `
                    <span class="text-xs text-gray-500 mb-1">${time}</span>
                    <div class="chat-bubble sent rounded-2xl rounded-br-md px-4 py-2 shadow-sm">
                        <p class="text-gray-800">${message}</p>
                    </div>
                `;
            } else {
                // 받은 메시지
                messageDiv.className = 'flex items-end space-x-2';
                messageDiv.innerHTML = `
                    <img src="sungmin.jpg" alt="상대방" class="w-8 h-8 rounded-full flex-shrink-0">
                    <div class="chat-bubble received rounded-2xl rounded-bl-md px-4 py-2 shadow-sm">
                        <p class="text-gray-800">${message}</p>
                    </div>
                    <span class="text-xs text-gray-500 mb-1">${time}</span>
                `;
            }
            
            // 타이핑 표시 제거
            const typingIndicator = chatArea.querySelector('.typing-indicator');
            if (typingIndicator) {
                typingIndicator.parentElement.parentElement.parentElement.remove();
            }
            
            chatArea.appendChild(messageDiv);
            chatArea.scrollTop = chatArea.scrollHeight;
        }

        // 타이핑 표시 추가 함수
        function addTypingIndicator() {
            const typingDiv = document.createElement('div');
            typingDiv.className = 'flex items-end space-x-2';
            typingDiv.innerHTML = `
                <img src="sungmin.jpg" alt="상대방" class="w-8 h-8 rounded-full flex-shrink-0">
                <div class="bg-white rounded-2xl rounded-bl-md px-4 py-3 shadow-sm">
                    <div class="flex space-x-1">
                        <div class="w-2 h-2 bg-gray-400 rounded-full typing-indicator"></div>
                        <div class="w-2 h-2 bg-gray-400 rounded-full typing-indicator" style="animation-delay: 0.2s;"></div>
                        <div class="w-2 h-2 bg-gray-400 rounded-full typing-indicator" style="animation-delay: 0.4s;"></div>
                    </div>
                </div>
            `;
            chatArea.appendChild(typingDiv);
            chatArea.scrollTop = chatArea.scrollHeight;
        }

        // AI API 호출 함수
        async function callAI(userMessage) {
            try {
                const response = await fetch(API_URL, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        message: userMessage
                    })
                });
                
                if (!response.ok) {
                    throw new Error(`API 호출 실패: ${response.status}`);
                }
                
                const data = await response.json();
                
                if (data.error) {
                    throw new Error(data.error);
                }
                
                // 디버깅 정보가 있으면 콘솔에 출력
                if (data.debug) {
                    console.log('디버깅 정보:', data.debug);
                }
                
                return data.response;
            } catch (error) {
                console.error('AI API 오류:', error);
                return "죄송해요, 응답을 생성하는 중에 오류가 발생했어요 😅";
            }
        }

        // 메시지 전송 함수
        async function sendMessage() {
            const message = messageInput.value.trim();
            if (message === '') return;
            
            // 사용자 메시지 추가
            addMessage(message, true);
            
            // 입력 필드 초기화
            messageInput.value = '';
            
            // 1초 후 타이핑 표시
            setTimeout(async () => {
                addTypingIndicator();
                
                try {
                    // AI API 호출
                    const aiResponse = await callAI(message);
                    
                    // 타이핑 표시 제거 후 AI 응답 추가
                    const typingIndicator = chatArea.querySelector('.typing-indicator');
                    if (typingIndicator) {
                        typingIndicator.parentElement.parentElement.parentElement.remove();
                    }
                    
                    addMessage(aiResponse, false);
                } catch (error) {
                    console.error('메시지 전송 오류:', error);
                    addMessage("죄송해요, 응답을 받아오는 중에 문제가 발생했어요 😅", false);
                }
            }, 1000);
        }

        // 전송 버튼 클릭 이벤트
        sendButton.addEventListener('click', sendMessage);
        
        // Enter 키 이벤트
        messageInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                sendMessage();
            }
        });

        // 페이지 로드 시 날짜와 활동 상태 설정
        document.addEventListener('DOMContentLoaded', function() {
            // 오늘 날짜 설정
            const dateElement = document.getElementById('current-date');
            if (dateElement) {
                dateElement.textContent = getCurrentDate();
            }
            
            // 활동 상태를 "지금 활동중"으로 변경
            const activityElement = document.getElementById('activity-status');
            if (activityElement) {
                activityElement.textContent = '지금 활동중';
            }
        });

        // 입력 필드 포커스
        messageInput.focus();
    </script>
</body>
</html>
