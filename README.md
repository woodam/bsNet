# bsNet

bsNet은 XMLHttpRequest를 이용한 AJAX 통신과 <a href='http://msdn.microsoft.com/en-us/library/ie/cc288060(v=vs.85).aspx' target='_blank'>**XDomainRequest**</a> 및 <a href='http://www.w3.org/TR/cors/' taret='_blank'>**CORS**</a>를 이용한 신개념 Cross Domain 통신을 지원하는 ProjectBS의 네트워크 통신 라이브러리 입니다.

현재 클라이언트 모듈은 JavaScript, 서버 모듈은 PHP로 구현되어 있으며, 앞으로 다양한 언어를 지원하고 기능을 확장할 계획입니다.

# 동작 원리

![](http://i.imgur.com/edMyZGD.png)

- 원칙적으로 도메인A에 있는 사용자는 도메인B 나 도메인C와 통신 불가 
- 도메인A에 있는 사용자는 같은 도메인A에 있는 bsJS에게 도메인C에 있는 자원을 요청
    - bsJS는 도메인B에 있는 crossproxy.php에게 사용자의 요청을 전달
    - corsproxy.php는 CORS 처리가 되어 있어 자신과 다른 도메인에 있는 bsJS와 통신 가능
    - 또한 crossproxy.php는 브라우저가 아닌 서버에 존재하여 Same Origin Policy의 제약을 받지 않고 도메인C의 자원에 접근 가능
- 따라서 도메인A의 사용자는 CORS에 대해 알지 못해도 도메인C의 자원에 접근 가능
- 도메인C에 있는 서비스 제공자는 CORS 처리를 해두지 않아도 도메인A에 있는 브라우저에 서비스 제공 가능

# 장점

bsNet은 단순한 기술 수준을 넘어 **웹 개발 프로젝트 진행 방법의 획기적인 변화**를 가져올 수 있습니다.

백엔드의 데이터를 기반으로 웹 서비스를 구축할 때 최근에는 DataSource 자체를 개방하지 않고 REST API를 개방합니다.
REST API를 호출하는 웹 서비스(REST API Consumer)는 주로 AJAX로 구현되므로, Same Origin Policy 정책 때문에 아래와 같이 Test(또는 Dev) 서버 내에 REST API Consumer와 REST API를 모두 배포해서 Test 서버 내에서 호출하게 됩니다.

![](http://i.imgur.com/wULPl4W.png)

하지만, Cross Domain 통신이 가능한 bsNet을 적용하면 개발 과정에서 배포를 할 필요 없이 바로 백엔드의 API를 호출할 수 있습니다.

![](http://i.imgur.com/tY69DCu.png)

개발을 마치고 운영으로 전환할 경우에도 bsNet을 적용해서 개발했던 소스를 고치지 않고, 운영 서버로 바로 이관하면 됩니다.

![](http://i.imgur.com/Bjy8RHp.png)

# ShowCase

bsNet의 예제는 작성 중 입니다.

# License

bsNet은 <a href='http://opensource.org/licenses/BSD-3-Clause' target='_blank'><b>BSD 라이선스</b></a>로 배포되는 Open Source Software 입니다.

bsNet의 모든 문서는 <a rel="license" href="http://creativecommons.org/licenses/by-nc-sa/4.0/" target='_blank'><b>크리에이티브 커먼즈 저작자표시-비영리-동일조건변경허락 4.0 국제 라이선스</b></a>에 따라 이용할 수 있습니다.

# Contact us

<a href='https://www.facebook.com/groups/bs5js/' target='_blank'>bsJS Facebook Group</a>


----------
Copyrightⓒ 2013, ProjectBS Committee. All rights reserved.

<a rel="license" href="http://creativecommons.org/licenses/by-nc-sa/4.0/" target='_blank'><img alt="크리에이티브 커먼즈 라이선스" style="border-width:0" src="http://i.creativecommons.org/l/by-nc-sa/4.0/88x31.png" /></a><br /><span xmlns:cc="http://creativecommons.org/ns#" property="cc:attributionName">ProjectBS</span>의 저작물인 이 저작물은(는) <a rel="license" href="http://creativecommons.org/licenses/by-nc-sa/4.0/" target='_blank'><b>크리에이티브 커먼즈 저작자표시-비영리-동일조건변경허락 4.0 국제 라이선스</b></a>에 따라 이용할 수 있습니다.<br />이 라이선스의 범위 이외의 이용허락을 얻기 위해서는 <a xmlns:cc="http://creativecommons.org/ns#" href="http://www.bsplugin.com" rel="cc:morePermissions" target='_blank'>http://www.bsplugin.com</a>을 참조하십시오.
