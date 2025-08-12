# 카카오톡 스타일 AI 챗봇

이성민과의 카카오톡 채팅방을 모방한 AI 챗봇 웹 애플리케이션입니다. 실제 카카오톡과 유사한 UI/UX를 제공하며, Google Gemini AI를 활용한 자연스러운 대화가 가능합니다.

## 🚀 주요 기능

- **카카오톡 스타일 UI**: 실제 카카오톡과 동일한 디자인과 애니메이션
- **실시간 채팅**: 타이핑 표시와 함께 자연스러운 대화 경험
- **AI 응답**: Google Gemini AI를 활용한 지능적인 응답
- **반응형 디자인**: 모바일과 데스크톱에서 모두 최적화된 경험
- **한국어 지원**: 완전한 한국어 인터페이스와 응답

## 📋 요구사항

- PHP 7.4 이상
- Node.js 16.0 이상
- npm 또는 yarn
- Google Gemini API 키

## 🛠️ 설치 방법

### 1. 저장소 클론
```bash
git clone <repository-url>
cd ai_chatbot
```

### 2. 의존성 설치
```bash
npm install
```

### 3. Tailwind CSS 빌드
```bash
npm run build
```

### 4. 환경 설정
`config.php` 파일을 생성하고 Google Gemini API 키를 설정합니다:

```php
<?php
define('GEMINI_API_KEY', 'your-gemini-api-key-here');
?>
```

### 5. 웹 서버 실행
PHP 내장 서버를 사용하여 실행:
```bash
php -S localhost:8000
```

또는 Apache/Nginx 웹 서버를 사용하여 실행

## 🎯 사용 방법

1. 웹 브라우저에서 `http://localhost:8000` 접속
2. 채팅 입력창에 메시지 입력
3. Enter 키 또는 전송 버튼 클릭
4. AI의 응답을 확인

## 📁 프로젝트 구조

```
ai_chatbot/
├── index.php              # 메인 HTML 페이지
├── api.php                # AI API 처리
├── gemini_handler.php     # Gemini AI 핸들러
├── config.php             # 설정 파일
├── tailwind.config.js     # Tailwind CSS 설정
├── src/
│   └── input.css          # Tailwind CSS 입력 파일
├── dist/
│   └── output.css         # 빌드된 CSS 파일
├── sungmin.jpg            # 프로필 이미지
├── kakao.png              # 파비콘
├── package.json           # npm 패키지 설정
├── README.md              # 프로젝트 문서
└── LICENSE                # 라이선스 파일
```

## 🔧 개발 스크립트

```bash
# Tailwind CSS 빌드
npm run build

# 개발 모드 (파일 변경 감지)
npm run watch
```

## 🎨 커스터마이징

### 색상 변경
`tailwind.config.js` 파일에서 카카오톡 색상을 수정할 수 있습니다:

```javascript
colors: {
  'kakao-yellow': '#FEE500',
  'kakao-gray': '#F2F3F5',
  // 추가 색상...
}
```

### AI 응답 스타일 변경
`gemini_handler.php` 파일에서 AI의 응답 스타일과 프롬프트를 수정할 수 있습니다.

## 🤝 기여하기

1. Fork the Project
2. Create your Feature Branch (`git checkout -b feature/AmazingFeature`)
3. Commit your Changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the Branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## 📄 라이선스

이 프로젝트는 MIT 라이선스 하에 배포됩니다. 자세한 내용은 [LICENSE](LICENSE) 파일을 참조하세요.

## 🙏 감사의 말

- [Tailwind CSS](https://tailwindcss.com/) - CSS 프레임워크
- [Google Gemini](https://ai.google.dev/) - AI 모델
- [Kakao](https://www.kakao.com/) - UI 디자인 영감

## 📞 문의

프로젝트에 대한 문의사항이 있으시면 이슈를 생성해 주세요.

---

**Note**: 이 프로젝트는 교육 및 개인 사용 목적으로 제작되었습니다. 상업적 사용 시 관련 법규를 준수해 주세요.
