function http( trim, detect, fn, mk ){
	var httpHeader = {}, head = [], paramBody = [], corsData = [], httpH = [], httpMethod,
	xhr = detect.browser === 'ie' && detect.browserVer < 9 ? (function(){
		var t0, i, j;
		t0 = 'MSXML2.XMLHTTP', t0 = ['Microsoft.XMLHTTP',t0,t0+'.3.0',t0+'.4.0',t0+'.5.0'], i = t0.length;
		while( i-- ){try{new ActiveXObject( j = t0[i] );}catch(e){continue;}break;}
		return function(){return new ActiveXObject(j);};
	})() : function(){return new XMLHttpRequest;},
	cors = W['XDomainRequest'] ? (function(){
		var mk = function( x, err ){return function(){x.ontimeout = x.onload = x.onerror = null, err ? ( x.abort(), end( null, err ) ) : end( x.responseText, x.contentType );};};
		return function( data, end ){
			var x = new XDomainRequest;
			x.ontimeout = mk( x, 'timeout'), x.timeout = timeout, x.onerror = mk( x, 'xdr error'), x.onload = mk( x, time), x.open( 'POST', CORSPROXY ), x.send(data);
		};
	})() : W['XMLHttpRequest'] ? function( data, end ){
		var x = xhr();
		async( x, end ), x.open( 'POST', CORSPROXY, true ),	x.setRequestHeader( 'Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8' ), x.withCredentials = true, x.send(data);
	} : 0,
	param = function( arg, i ){
		var t0, j, k, v, m;
		if( !arg || ( j = arg.length ) < i + 1 ) return '';
		head.corsKey = head.length = paramBody.length = 0;
		while( i < j ){
			if( !( k = k.replace( trim, '' ) ).length ) err(5005);
			if( i < j ){
				v = arg[i++],
				k.charAt(0) === '@' ? head.push( k.substr(1), typeof v == 'function' ? v(httpMethod) : v ) :
				k == 'corsAccessKey' ? head.corsKey = v :
					paramBody[paramBody.length] = encodeURIComponent(k) + '=' + encodeURIComponent(( v && typeof v == 'object' ? JSON.stringify(v) : v + '' ).replace( trim, '' ));
			}else m = encodeURIComponent(k);
		}
		return m || paramBody.join('&');
	},
	url = function( U, arg ){
		var t0 = U.replace( trim, '' ).split('#'), p = param( arg, 2 );
		return t0[0] + ( t0[0].indexOf('?') > -1 ? '&' : '?' ) + 'bsNC=' + bs.rand( 1000, 9999 ) + ( p ? '&' + p : '' ) + ( t0[1] ? '#' + t0[1] : '' );
	},
	async = function( x, end ){
		var timeId = setTimeout( function(){
			if( timeId == -1 ) return;
			if( x.readyState !== 4 ) x.abort();
			timeId = -1, x.onreadystatechange = null, end( null, 'timeout' );
		}, timeout );
		x.onreadystatechange = function(){
			var text, status;
			if( x.readyState !== 4 || timeId == -1 ) return;
			clearTimeout(timeId), timeId = -1,
			text = x.status === 200 || x.status === 0 ? x.responseText : null,
			status = text ? x.getAllResponseHeaders() : x.status,
			x.onreadystatechange = null, end( text, status );
		};
	},
	httpMethod, http = function( method, end, U, arg ){
		var x, key, i, j, k;
		if( ( httpMethod = method ) === 'GET' ){
			if( ( U = url( U, arg ) ).length > 512 ) err( 5004, U );
			arg = '';
		}else U = url(U), arg = param( arg, 2 );
		if( ( i = U.indexOf( '://' ) ) > -1 && U.substr( i + 3, ( j = location.hostname).length ) != j ){
			if( !end || !cors ) return err(5001);
			for( key = head.corsKey || CORSPROXYKEY, corsData.length = httpH.length = i = 0, j = head.length ; i < j ; i += 2 )
				httpHeader[corsData[i] = k = head[i]] ? httpH[httpH.length] = k : 0, corsData[i + 1] = head[i + 1];
			k = i;
			for( i in httpHeader ) if( httpH.indexOf(i) == -1 ) corsData[k++] = i, corsData[k++] = httpHeader[i];
			k = param( corsData, 0 ), corsData.length = 0,
			corsData.push( 'url', U, 'method', method, 'key', key, 'cookie', document.cookie, 'data', arg, 'header', k );
			cors( param(corsData, 0), end );
		}else{
			x = xhr();
			if( end ) async( x, end );
			x.open( method, U, end ? true : false ),
			httpH.length = i = 0, j = head.length;
			while( i < j ){
				x.setRequestHeader( k = head[i++], head[i++] );
				if( httpHeader[k] ) httpH[httpH.length] = k;
			}
			for( i in httpHeader ) if( httpH.indexOf(i) == -1 ) x.setRequestHeader( i, httpHeader[i] );
			x.send(arg);
			if( !end ) return i = x.responseText, x.onreadystatechange = null, i;
		}
	};
	mk = function(m){return function( end, url ){return http( m, end, url, arguments );};};
	fn( 'post', mk('POST') ), fn( 'put', mk('PUT') ), fn( 'delete', mk('DELETE') ), fn( 'get', mk('GET') ),
	fn( 'header', function( k, v ){httpHeader[k] ? err( 2200, k ) : httpHeader[k] = v;} );
}