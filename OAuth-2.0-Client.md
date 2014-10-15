# bsNet OAuth 2.0 Client 작성

## OAuth 2.0

### 등장 인물

- Resource Owner : 사용자
- Resource Server : API 서버
- Authorization Server : 인증 서버(API 서버와 같을 수도 있음)
- Client : 클라이언트(일반적인 서버/클라이언트와의 혼동을 피하기 위해 여기서는 'OAuth 클라이언트'라고 한다.)
    - Confidential Client : 웹 서버에서 돌고 있는 서버 애플리케이션(Java, PHP, ...)
    - Public Client : 브라우저 기반 애플리케이션(JavaScript)과 Native 애플리케이션(Mobile ...)  

### OAuth 인증 방식

- Authorization Code
	- OAuth 클라이언트가 Confidential Client 인 경우 이 방식으로 인증
	- response_type=code
	- OAuth 클라이언트가 인증 요청을 인증 서버로 날리면
	- 인증 서버가 OAuth 클라이언트가 지정한 redirect_uri 에 Query String으로 code=CODE_VALUE 반환
	- OAuth 클라이언트는 앞에서와 동일한 redirect_uri와 CODE_VALUE를 파라미터로 하여 인증 서버에 access_token을 요청
	- 인증 서버가 redirect_uri 에 access_token 반환
	- OAuth 클라이언트는 API 서버가 요청하는 방식(보통 특정 헤더 이름 지정하여 HTTP 헤더로 전달하게 함)에 맞게 access_token을 API 호출에 사용되는 파라미터와 함께 API 서버에 전송하면서 API 호출
	- API 서버는 API 호출 결과 반환
	- **나중에 그림 찾아서 추가**
- Implicit
	- OAuth 클라이언트가 Public Client 인 경우 이 방식으로 인증
	- response_type=token
	- Authorization Code 방식과 달리 중간에 code를 통하지 않고 인증 서버가 한 번에 access-token을 반환
	- OAuth 클라이언트는 API 서버가 요청하는 방식(보통 특정 헤더 이름 지정하여 HTTP 헤더로 전달하게 함)에 맞게 access_token을 API 호출에 사용되는 파라미터와 함께 API 서버에 전송하면서 API 호출
	- API 서버는 API 호출 결과 반환
	- **나중에 그림 찾아서 추가**
- Resource Owner Password Credentials
	- 넘어감
- Client Credentials
	- 넘어감

## bsNet OAuth 2.0 Client

### 인증 방식

사용자가 bsNet의 crossproxy를 이용한다는 것은 사용자 애플리케이션이 Same Origin Policy의 제약을 받는 Public Client라는 것을 의미하므로 bsNet의 OAuth 2.0 client 는 Implicit 인증 방식을 기준으로 작성한다.

### 만들어야 할 것

- bsJS, crossproxy를 통해 access_token을 요청하는 html - (A)
- (A)가 전송한 access_token 요청용 정보를 가공해서 인증 서버로 요청을 날리는 crossproxy 안의 추가 로직
- access_token을 받아서 다시 bsJS, crossproxy를 통해 API를 호출하는 html(redirect_uri가 되는 html) - (B)
- (B)가 전송한 API 호출용 정보를 가공해서 API 서버로 요청을 날리고 결과값을 받아 (B)에 반환하는 crossproxy 안의 추가 로직

### access_token 요청 html

#### bsJS 호출

bs.post() 파라미터로 OAuth 2.0 Spec에 따른 아래의 항목 추가

- response_type - 필수
- client_id - 필수
- redirect_uri - Spec상 필수는 아니나 사실 상 필수
- scope - 선택
- state - 선택

필수, 선택 여부는 인증 서버에서 정한 규약에 따른다.
추후 주요 API 서버(Google, Facebook, Twitter, Naver, Dropbox, ...)도 별도의 식별자를 두어 파라미터로 전달하고, 해당 API 서버 처리를 위한 내부로직을 재사용할 수 있도록 라이브러리화

#### custom protocol은 고려 대상에서 제외

google://API-URL/parameter, dropbox://API-URL/parameter 과 같은 형식의 custom protocol 사용을 검토한 바 있으나, Implicit 인증 방식의 경우 보안 문제로 프로토콜을 https로 강제하는 API 서버(Google)도 있으므로, custom protocol 은 고려 대상에서 제외한다. 

### crossproxy의 access_token 요청 로직 

access_token 요청

- bsJS를 통해 access_token 요청 html이 보내준 인증용 정보를 받아서
- 인증 서버에서 정한 규약에 맞게 가공하여 인증 서버로 전달하고,
	- 인증 서버는 redirect_uri로 access_token 반환 

### redirect_uri

인증 서버가 반환해준 access_token 를 받아서 bsJS, crossproxy를 통해 API 호출하는 html

- bsJS를 통해 access_token과 API 호출용 파라미터를 crossproxy에 전송

### crossproxy의 API 호출 로직

access_token과 API 요청용 파라미터로 외부 API를 호출하고 결과를 redirect_uri로 반환

- bsJS를 통해 redirect_uri가 보낸 access_token을 API 서버가 정한 규약에 맞게 가공해서,
- API 호출 파라미터와 함께 API 서버에 있는 API 를 호출하고,
- 결과를 redirect_uri로 반환


----------
Copyrightⓒ 2013, ProjectBS Committee. All rights reserved.

<a rel="license" href="http://creativecommons.org/licenses/by-nc-sa/4.0/" target='_blank'><img alt="크리에이티브 커먼즈 라이선스" style="border-width:0" src="http://i.creativecommons.org/l/by-nc-sa/4.0/88x31.png" /></a><br /><span xmlns:cc="http://creativecommons.org/ns#" property="cc:attributionName">ProjectBS</span>의 저작물인 이 저작물은(는) <a rel="license" href="http://creativecommons.org/licenses/by-nc-sa/4.0/" target='_blank'><b>크리에이티브 커먼즈 저작자표시-비영리-동일조건변경허락 4.0 국제 라이선스</b></a>에 따라 이용할 수 있습니다.<br />이 라이선스의 범위 이외의 이용허락을 얻기 위해서는 <a xmlns:cc="http://creativecommons.org/ns#" href="http://www.bsplugin.com" rel="cc:morePermissions" target='_blank'>http://www.bsplugin.com</a>을 참조하십시오.
