/* bsNet v0.2
 * Copyright (c) 2013 by ProjectBS Committe and contributors. 
 * http://www.bsplugin.com All rights reserved.
 * Licensed under the BSD license. See http://opensource.org/licenses/BSD-3-Clause
 */
'use strict';
if( !this['console'] ) this['console'] = {log:function(){}};
var CROSSPROXY = 'http://api.bsplugin.com/bsNet/php/crossProxy.0.2.php',  
	CROSSPROXYKEY = 'CROSSPROXY_DEMO_ACCESS_KEY', bsNet = {}, isDebug = 0,
	err = function( num, msg ){console.log( num, msg ); if( isDebug ) throw new Error( num, msg );}, 
	bs = bs || {}, timeout = 5000, fn = function( k, v ){bsNet[k] = v;};
	bs['rand'] || (function(){
		var rc, rand = function(){return rc = ( ++rc ) % 1000, rand[rc] || ( rand[rc] = Math.random() );};
		bs['rand'] = function( a, b ){ return parseInt( rand() * ( b - a + 1 ) ) + a; };
	})();
(function( W, trim, detect, fn, mk ){
var xhrType = 0, xhr = detect.browser === 'ie' && detect.browserVer < 9 ? (function(){
		var t0, i, j;
		xhrType = 1;
		t0 = 'MSXML2.XMLHTTP', t0 = ['Microsoft.XMLHTTP',t0,t0+'.3.0',t0+'.4.0',t0+'.5.0'], i = t0.length;
		while( i-- ){try{new ActiveXObject( j = t0[i] );}catch(e){continue;}break;}
		return function(){return new ActiveXObject(j);};
	})() : function(){return new XMLHttpRequest;},
	cross = W['XDomainRequest'] ? (function(){
		var mk = function( x, err, end ){return function(){x.ontimeout = x.onload = x.onerror = null, err ? ( x.abort(), end( null, err ) ) : end(x.responseText);};};
		return function( data, end ){
			var x = new XDomainRequest;
			x.ontimeout = mk( x, 'timeout', end ), x.timeout = timeout, x.onerror = mk( x, 'xdr error', end ), x.onload = mk( x, 0, end ), x.open( 'POST', CROSSPROXY ), x.send(data);
		};
	})() : W['XMLHttpRequest'] ? function( data, end ){
		var x = xhr();
		async( x, end ), x.open( 'POST', CROSSPROXY, true ), x.setRequestHeader( 'Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8' ), x.withCredentials = true, x.send(data);
	} : 0,
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
			text = x.status == 200 || x.status == 0 ? x.responseText : null,
			status = text ? x.getAllResponseHeaders() : x.status,
			xhrType ? delete x.onreadystatechange : x.onreadystatechange = null,
			end( text, status );
		};
	},
	head = [], paramBody = [], paramHeader = function(v){return typeof v == 'function' ? v(httpMethod) : v;},
	param = function( arg, i ){
		var t0, j, k, v, m;
		if( !arg || ( j = arg.length ) < i + 1 ) return '';
		head.crossKey = head.length = paramBody.length = 0;
		while( i < j ){
			if( !( k = arg[i++].replace( trim, '' ) ).length ) err(5005);
			if( i < j ){
				v = arg[i++],
				k.charAt(0) === '@' ? (console.log('head'),head.push( k.substr(1), paramHeader(v) )) :
				k == 'crossAccessKey' ? head.crossKey = v :
					paramBody[paramBody.length] = encodeURIComponent(k) + '=' + encodeURIComponent(( v && typeof v == 'object' ? JSON.stringify(v) : v + '' ).replace( trim, '' ));
			}else m = encodeURIComponent(k);
		}
		return m || paramBody.join('&');
	},
	httpCross = cross ? [] : 0, httpH = [], httpMethod,
	http = function( method, end, U, arg ){
		var x, key, i, j, k;
		if( ( httpMethod = method ) === 'GET' ){
			if( ( U = url( U, arg ) ).length > 512 ) err( 5004, U );
			arg = '';
		}else U = url(U), arg = param( arg, 2 );
		if( ( i = U.indexOf( '://' ) ) > -1 && U.substr( i + 3, ( j = location.hostname).length ) != j ){
			if( !end || !cross ) return err(5001);
			for( key = head.crossKey || CROSSPROXYKEY, httpCross.length = httpH.length = i = 0, j = head.length ; i < j ; i += 2 )
				baseHeader[httpCross[i] = k = head[i]] ? httpH[httpH.length] = k : 0, httpCross[i + 1] = head[i + 1];
			k = i;
			for( i in baseHeader ) if( httpH.indexOf(i) == -1 ) httpCross[k++] = i, httpCross[k++] = paramHeader(baseHeader[i]);
			k = param( httpCross, 0 ), httpCross.length = 0,
			httpCross.push( 'url', U, 'method', method, 'key', key, 'data', arg, 'header', k );
			cross( param(httpCross, 0), end );
		}else{
			x = xhr();
			if( end ) async( x, end );
			x.open( method, U, end ? true : false ),
			httpH.length = i = 0, j = head.length;
			while( i < j ){
				x.setRequestHeader( k = head[i++], head[i++] );
				if( baseHeader[k] ) httpH[httpH.length] = k;
			}
			for( i in baseHeader ) if( httpH.indexOf(i) == -1 ) x.setRequestHeader( i, paramHeader(baseHeader[i]) );
			x.send(arg);
			if( !end ) return ( i = x.responseText ), xhrType ? delete x.onreadystatechange : x.onreadystatechange = null, i;
		}
	}, baseHeader = {};
	fn( 'header', function( k, v ){baseHeader[k] ? err( 2200, k ) : baseHeader[k] = v;} ),
	mk = function(m){return function( end, url ){return http( m, end, url, arguments );};},
	fn( 'post', mk('POST') ), fn( 'put', mk('PUT') ), fn( 'delete', mk('DELETE') ), fn( 'get', mk('GET') );
})( this, /^\s*|\s*$/g,  {browser:this['XMLHttpRequest'] ? '!ie' : 'ie', browserVer:7}, fn );
fn = bsNet.header,
fn( 'Cache-Control', 'no-cache' ),
fn( 'Content-Type', function(type){return ( type == 'GET' ? 'text/plain' : 'application/x-www-form-urlencoded' ) + '; charset=UTF-8';} );
