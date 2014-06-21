function http( trim, detect, fn, mk ){
	var httpHeader = {}, head = [], paramBody = [], corsHeader = [], corsBody = [], httpH = [], 
	xhr = detect.browser === 'ie' && detect.browserVer < 9 ? (function(){
		var t0, i, j;
		t0 = 'MSXML2.XMLHTTP', t0 = ['Microsoft.XMLHTTP',t0,t0+'.3.0',t0+'.4.0',t0+'.5.0'], i = t0.length;
		while( i-- ){try{new ActiveXObject( j = t0[i] );}catch(e){continue;}break;}
		return function(){return new ActiveXObject(j);};
	})() : function(){return new XMLHttpRequest;},
	corsRun, cors = W['XDomainRequest'] ? ( 
		corsRun = (function(){ 
			var async = function( x, end ){
				var timeId, clr;
				clr = function(){
					if( timeId > -1 ) clearTimeout(timeId);
					timeId = -1, x.onload = x.onerror = null;
				}, x.onload = function(){
					if( timeId < 0 ) return;
					clr(), end( x.responseText, 200 );
				}, x.onerror = function(){
					if( timeId > -1 ) x.abort(), clr();
					end( null, 'xdr error' );
				}, timeId = setTimeout( function(){
					if( timeId > -1 ) x.abort(), clr(), end( null, 'timeout' );
				}, timeout );
			};
			return function( x, arg, end ){
				async( x, end );
				x.open( 'POST', CORSPROXY );
				x.send(arg);
			};
		})(), function(){ return new XDomainRequest; }
	) : W['XMLHttpRequest'] ? (
		corsRun = function( x, arg, end ){
			async( x, end );
			x.open( 'POST', CORSPROXY, true ),
			x.setRequestHeader( 'Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8' ); 
			x.withCredentials = true;
			x.send(arg);
		}, function(){ return new XMLHttpRequest; }
	) : 0,
	param = function(arg){
		var i, j, k, v, m;
		if( !arg || ( j = arg.length ) < 3 ) return '';
		head.length = paramBody.length = 0, i = 2;
		while( i < j ){
			if( typeof( k = arg[i++] ) === 'string' && ( k = k.replace( trim, '' ) ).length !== 0 ){}else{err( 5005 );}
			if( i < j ){
				v = typeof( v = arg[i++] ) === 'string' ? v.replace( trim, '' ) : typeof v === 'number' || typeof v === 'boolean' ? v.toString() : typeof v === 'undefined' ? 'undefined' : typeof v === 'function' ? v.toString() : v === null ? 'null' : JSON.stringify(v);
				k.charAt(0) === '@' ? ( k = k.substr(1).replace( trim, '' ) ).length === 0 ? err( 5006 ) : ( head[head.length] = k, head[head.length] = v ) : paramBody[paramBody.length] = encodeURIComponent(k) + '=' + encodeURIComponent(v);
			}else m = encodeURIComponent( k );
		}
		return m || paramBody.join('&');
	},
	url = function( url, arg ){
		var t0 = url.replace( trim, '' ).split('#'), p = param(arg);
		return t0[0] + ( t0[0].indexOf('?') > -1 ? '&' : '?' ) + 'bsNC=' + bs.rand( 1000, 9999 ) + ( p ? '&' + p : '' ) + ( t0[1] ? '#' + t0[1] : '' );
	},
	async = function( x, end ){
		var timeId;
		x.onreadystatechange = function(){
			var text, status;
			if( x.readyState !== 4 || timeId < 0 ) return;
			clearTimeout(timeId), timeId = -1,
			text = x.status === 200 || x.status === 0 ? x.responseText : null,
			status = text ? x.getAllResponseHeaders() : x.status,
			x.onreadystatechange = null, end( text, status );
		}, timeId = setTimeout( function(){
			if( timeId > -1 ){
				if( x.readyState !== 4 ) x.abort();
				timeId = -1, x.onreadystatechange = null, end( null, 'timeout' );
			}
		}, timeout );
	},
	http = function( type, end, U, arg ){
		var x, key, i, j, k;
		if( type === 'GET' ){
			U = url( U, arg ), arg = '';
			if( U.length > 512 ) err( 5004 );
		} else U = url( U ), arg = param( arg );
		if( U.indexOf( '://' ) > -1 ? U.slice(0,7) === 'http://' || U.slice(0,8) === 'https://' ? U.substring(U.indexOf('://')+3).slice(0, location.hostname.length) === location.hostname.domain ? 0 : 1 : 1 : 0 ){
			if( !end ) err( 5002 );
			x = cors() || err( 5001 );
			key = ( i = head.indexOf( 'corsAccessKey' ) ) > - 1 ? ( head.splice( i, 2 ), head[i+1]  ) : CORSPROXYKEY ? CORSPROXYKEY : err( 5003 );
			corsBody.length = corsHeader.length = httpH.length = 0, i = 2, j = head.length + 2;
			while( i < j ){
				corsHeader[i++] = k = head[i-3], corsHeader[i++] = head[i-3];
				if( httpHeader[k] ) httpH[httpH.length] = k;
			}
			k = i; for( i in httpHeader ) if( httpH.indexOf(i) === -1 ) j = httpHeader[i], corsHeader[k++] = i, corsHeader[k++] = typeof j === 'function' ? j(type) : j;
			i = 2, corsBody[i++] = 'url', corsBody[i++] = U, corsBody[i++] = 'method', corsBody[i++] = type, 
			corsBody[i++] = 'key', corsBody[i++] = key, corsBody[i++] = 'cookie', corsBody[i++] = document.cookie,
			corsBody[i++] = 'data', corsBody[i++] = arg, corsBody[i++] = 'header', corsBody[i++] = param(corsHeader);
			corsRun( x, param(corsBody), end );
		}else{
			x = xhr();
			if( end ) async( x, end );
			x.open( type, U, end ? true : false ),
			httpH.length = i = 0, j = head.length;
			while( i < j ){
				x.setRequestHeader( k = head[i++], head[i++] );
				if( httpHeader[k] ) httpH[httpH.length] = k;
			}
			for( i in httpHeader ) if( httpH.indexOf(i) === -1 ) j = httpHeader[i], x.setRequestHeader( i, typeof j === 'function' ? j(type) : j );
			x.send(arg);
			if( !end ) return i = x.responseText, x.onreadystatechange = null, i;
		}
	};
	mk = function(m){ return function( end, url ){ return http( m, end, url, arguments ); }; };
	fn( 'post', mk('POST') ), fn( 'put', mk('PUT') ), fn( 'delete', mk('DELETE') ), fn( 'get', mk('GET') ),
	fn( 'header', function( k, v ){httpHeader[k] ? err( 2200, k ) : httpHeader[k] = v;} );
}