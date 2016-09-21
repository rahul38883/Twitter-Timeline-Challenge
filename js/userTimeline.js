var xhr_search, xhr_follower;
var window_search_close = 1;

$(document).ready(function(){
	
	$.post('src/getData.php', {type:1}, function(response){
		response = format_text(response);
		response = JSON.parse(response);
			
		if(response.status=='error'){
			console.error(response.data);
		}else if(response.status=='redirect'){
			window.location.href = response.data.url;
		}else{
			user = response.data;
			$("#profile_pic_img").attr("src", user.profile_image_url);
			$("#user_name").text(user.name);
			$("#user_uname").text('@'+user.screen_name);
			this_user = user.screen_name;
		}
	});
	
	$.post('src/getData.php', {type:2}, function(response){
		response = format_text(response);
		response = JSON.parse(response);
		
		a = response;
		if(response.status=='error'){
			console.error(response.data);
		}else{
			display_tweets(response.data);
		}
		
	});
	
	$.post('src/getData.php', {type:3}, function(response){
		response = format_text(response);
		response = JSON.parse(response);
		
		if(response.status=='error'){
			console.error(response.data);
		}else{
			
			var data = response.data;
			var name = '';
			var uname = '';
			var profile_pic = '';
			var profile_banner = '';
			var description = '';
			var urls = '';
			var following = false;
			
			data.users.forEach(function(item){
				
				name = item.name;
				uname = item.screen_name;
				description = item.description;
				profile_pic = item.profile_image_url;
				if(item.profile_banner_url){
					profile_banner = item.profile_banner_url;
				}else{
					profile_banner = item.profile_background_image_url;
				}
				urls = '';
				
				if(item.entities.url && item.entities.url.urls && item.entities.url.urls.length){
					var length = item.entities.url.urls.length;
					item.entities.url.urls.forEach(function(item, index){
						urls += '<a target="_blank" href="'+item.url+'">'+item.display_url+'</a>';
						urls += index==length-1?'':'<br>';
					});
				}
				
				following = item.following;
				
				$('#followers_list').append(createFollowerHTML({
					profile_banner : profile_banner,
					profile_pic : profile_pic,
					name : name,
					uname : uname,
					description : description,
					urls : urls,
					following : following
				}));
				
			});
			
		}
	});
	
	$(document.body).on('click', '.follower_user_info', function(){
		var uname = $(this).attr('value');
		display_follower_tweets(uname);
	});
	
	$(window).click(function(){
		if(window_search_close == 1){
			$('#followers_dropdown').hide();
		}
	});
	
	$('#follower_input').focus(function(e){
		window_search_close = 0;
		if($('#followers_dropdown').find('div.search_result_items').length){
			$('#followers_dropdown').show();
		}
	});
	
	$('#follower_input').blur(function(){
		window_search_close = 1;
	});
	
	$(document.body).on('click', '.search_result_items', function(e){
		$('#followers_dropdown').hide();
		var uname = $(this).attr('value');
		display_follower_tweets(uname);
		e.stopPropagation();
	});
	
	$('#follower_input').keyup(function(e){
		var input = $('#follower_input').val();
		
		if(xhr_search && xhr_search.readyState != 4){
			xhr_search.abort();
		}
		
		if(input.length > 0){
			xhr_search = $.post('src/fetch_user.php', {value:input}, function(response){
				response = JSON.parse(response);
				if(response.status == 'error'){
					console.error(response.data);
				}else if(response.status == 'redirect'){
					window.location.href = response.data.url;
				}else{
					$('#followers_dropdown').empty();
					if(response.data.length == 0){
						$('#followers_dropdown').hide();
					}else{
						$('#followers_dropdown').show();
					}
					response.data.forEach(function(item){
						$('#followers_dropdown').append(''+
							'<a href="#user_timeline">'+
								'<div class="search_result_items" value="'+item.follower_username+'">'+
									'<span class="search_result_name">'+item.follower_name+'</span> '+
									'(@<span class="search_result_uname">'+item.follower_username+'</span>)'+
								'</div>'+
							'</a>'+
						'');
					});
				}
			});
		}else{
			$('#followers_dropdown').empty();
			$('#followers_dropdown').hide();
		}
	});
	
	$('#change_cache_duration').change(function(){
		var value = $(this).val();
		$.post('src/change_cache_duration.php', {cache_duration:value}, function(response){
			//
		});
	});
	
	$('.navbar_ul>li').click(function(){
		$('.navbar_ul>li').removeClass('active');
		$(this).addClass('active');
	});
	
	$(document.body).on('click', '#download_tweets_btn', function(){
		var value = $('#download_format').find(':selected').attr('value');
		$.post('src/getData.php', {type:4, screen_name:this_user, count:10}, function(response){
			response = JSON.parse(response);
			
			if(response.status=='error'){
				console.error(response.data);
			}else if(response.status=='redirect'){
				window.location.href = response.data.url;
			}else{
				
				var obj = [];
				
				if(response.data.length == 0){
					console.error('no tweets found');
					return;
				}
				
				response.data.forEach(function(item){
					var retweeted_item = item.retweeted_status;
					
					var media = [];
					var urls = [];
					
					if(item.extended_entities && item.extended_entities.media && item.extended_entities.media.length){
						item.extended_entities.media.forEach(function(item){
							if(item.type=='video'){
								media[media.length] = {
									type : 'video',
									source : item.video_info.variants[0].url
								};
							}else{
								media[media.length] = {
									type : 'image',
									url : item.media_url
								};
							}
						});
					}else if(item.entities.media && item.entities.media.length){
						item.entities.media.forEach(function(item){
							media[media.legnth] = {
								type : 'image',
								url : item.media_url
							};
						});
					}
					
					if(item.entities.urls && item.entities.urls.length){
						item.entities.urls.forEach(function(item){
							urls[urls.length] = item.url;
						});
					}
					
					obj[obj.length] = {
						retweeted : retweeted_item?'1':'0',
						retweeted_person_name : retweeted_item?retweeted_item.user.name:'',
						retweeted_person_screen_name : retweeted_item?retweeted_item.user.screen_name:'',
						name : item.user.name,
						screen_name : item.user.screen_name,
						original_time : retweeted_item?retweeted_item.created_at:item.created_at,
						retweeted_time : retweeted_item?item.created_at:'',
						text : (retweeted_item?retweeted_item.text:item.text),
						favorite_count : item.favorite_count,
						retweet_count : item.retweet_count,
						place : item.place?item.place.full_name:'',
						media : media,
						links : urls
					};
				});
				
				convert_data(value, obj);
				
			}
			
		});
	});
	
});

	function convert_data(type, obj){
		
		/*
		*	type =
		*		1 : csv
		*		2 : xls
		*		3 : google-spreadhseet
		*		4 : pdf
		*		5 : xml
		*		6 : json
		*/
		
		var href = '';
		var filename = 'Tweets';
		var extension = '';
		var no_click = 0;
		
		if(type==1){
			
			var result = 'data:text/csv;charset=utf-8,';
			var keys = Object.keys(obj[0]);
			result += keys.join(',')+'\n';
			
			obj.forEach(function(item, index){
				keys.forEach(function(key, index){
					
					var temp_item = '';
					
					if(key=='media'){
						
						item[key].forEach(function(item, index){
							if(item.type == 'video'){
								temp_item += index > 0 ? '\n'+item.source : item.source;
							}else if(item.type=='image'){
								temp_item += index > 0 ? '\n'+item.url : item.url;
							}
						});
						
					}else if(key=='links'){
						
						item[key].forEach(function(item, index){
							temp_item += index > 0 ? '\n'+item : item;
						});
						
					}else{
						temp_item += item[key];
					}
					
					temp_item = "\""+temp_item+"\"";
					result += (index > 0 ? ','+temp_item : temp_item);
					
				});
				
				if(index != obj.length-1) result += '\n';
			});
			
			href = encodeURI(result);
			extension = 'csv';
			
		}else if(type==2 || type==4){
			
			var temp_html;
			//temp_html += '<table>';
			var keys = Object.keys(obj[0]);
			
			temp_html += '<tr>';
			keys.forEach(function(item){
				temp_html += '<td>'+item+'</td>';
			});
			temp_html += '</tr>';
			
			obj.forEach(function(item, index){
				
				temp_html += '<tr>';
				
				keys.forEach(function(key, index){
					
					temp_html += '<td>';
					
					if(key=='media'){
						
						item[key].forEach(function(item, index){
							if(item.type == 'video'){
								temp_html += index > 0 ? '<br>'+item.source : item.source;
							}else if(item.type=='image'){
								temp_html += index > 0 ? '<br>'+item.url : item.url;
							}
						});
						
					}else if(key=='links'){
						
						item[key].forEach(function(item, index){
							temp_html += index > 0 ? '<br>'+item : item;
						});
						
					}else{
						temp_html += item[key];
					}
					
					temp_html += '</td>';
					
				});
				
				temp_html += '</tr>';
				
			});
			
			//temp_html += '</table>';
			
			if(type==2){
				
				//temp_html = temp_html.replace(/ /g, '%20');
				$('#donwloadTable').html(temp_html);
				$('#donwloadTable').tableExport({
					type:'excel',
					htmlContent:'true',
					filename:'tweets'
				});
				no_click = 1;
				
			}else if(type==4){
				
				var newObj = {
					keys : Object.keys(obj[0]),
					values : []
				};
				obj.forEach(function(item, index){
					var arr = [];
					for(var key in item){
						if(key == 'media'){
							var arrr = [];
							item[key].forEach(function(item){
								arrr[arrr.length] = item["source"]?item["source"]:item["url"];
							});
							arr[arr.length] = arrr;
						}else{
							arr[arr.length] = item[key];
						}
					}
					newObj.values[index] = arr;
				});
				$('#obj_var').val(JSON.stringify(newObj));
				$('#pdf_filename').val(filename);
				$('#pdf_form').submit();
				no_click = 1;
				
			}
			
		}else if(type==3){
			
			//google-spreadhseet
			
			no_click = 1;
			
			$.post('src/google_auth.php', {tweet_data:JSON.stringify(obj), filename:filename, worksheetname:'TweetsRecord' }, function(response){
				response = JSON.parse(response);
				if(response.status == "success"){
					window.open(response.data.auth_url, '_blank');
				}else{
					console.error("something went wrong with google auth!!");
				}
			});
			
		}else if(type==5){
			
			//xml
			
			var xml_data = '<?xml version="1.0" encoding="UTF-8"?>'
			xml_data += '<tweetdata>';
			
			obj.forEach(function(item){
				
				xml_data += '<tweet>';
				
				if(item.retweeted==1){
					xml_data += '<retweetInfo>';
					xml_data += '<name>'+item.retweeted_person_name+'</name>';
					xml_data += '<screenName>'+item.retweeted_person_screen_name+'</screenName>';
					xml_data += '<time>'+item.retweeted_time+'</time>';
					xml_data += '</retweetInfo>';
				}
				
				xml_data += '<name>'+item.name+'</name>';
				xml_data += '<screenName>'+item.screen_name+'</screenName>';
				xml_data += '<time>'+item.original_time+'</time>';
				xml_data += '<text>'+item.text+'</text>';
				xml_data += '<favorite>'+item.favorite_count+'</favorite>';
				xml_data += '<retweet>'+item.retweet_count+'</retweet>';
				
				if(item.place)
					xml_data += '<place>'+item.place+'</place>';
				
				if(item.media && item.media.length){
					xml_data += '<media>';
					
					item.media.forEach(function(item){
						xml_data += '<'+item.type+'>';
						xml_data += '<url>'+(item.source?item.source:item.url)+'</url>';
						xml_data += '</'+item.type+'>';
					});
					
					xml_data += '</media>';
				}
				
				if(item.links && item.links.length){
					xml_data += '<links>';
					
					item.links.forEach(function(item){
						xml_data += '<url>'+item+'</url>';
					});
					
					xml_data += '</links>';
				}
				
				xml_data += '</tweet>';
				
			});
			
			xml_data += '</tweetdata>';
			
			var blob = new Blob([xml_data], {type:'text/xml'});
			saveAs(blob, filename+'.xml');
			no_click = 1;
			
		}else if(type==6){
			
			var json_data = JSON.stringify(obj);
			var blob = new Blob([json_data], {type:'application/json'});
			saveAs(blob, filename+'.json');
			no_click = 1;
			
		}
		
		if(!no_click){
			var temp = document.getElementById('donwloadLink');
			temp.setAttribute('href', href);
			temp.setAttribute('download', filename+'.'+extension);
			temp.click();
		}
		
	}
	
	function display_tweets(data){
		
		$('#tweet_main_box').empty();
		
		data.forEach(function(item, index){
				
			var text = item.text;
			var name = item.user.name;
			var uname = item.user.screen_name;
			var profile_image_url = item.user.profile_image_url;
			var favorite_count = item.favorite_count;
			var retweet_count = item.retweet_count;
			var time = item.created_at;
			var media = '';
			var urls = '';
			var place = '';
			
			if(item.extended_entities && item.extended_entities.media && item.extended_entities.media.length){
				var type = item.extended_entities.media.length;
				item.extended_entities.media.forEach(function(item){
					if(item.type=='video'){
						var sources = '';
						item.video_info.variants.forEach(function(item){
							sources+='<source type="'+item.content_type+'" src="'+item.url+'">';
						});
						media+='<video type="'+type+'" class="tweet_media_source" controls poster="'+item.media_url+'">'+
									sources+
								'</video>';
					}else{
						media+='<img type="'+type+'" class="tweet_media_source" src="'+item.media_url+'"></img>';
					}
				});
			}else if(item.entities.media && item.entities.media.length){
				var type = item.extended_entities.media.length;
				item.entities.media.forEach(function(item){
					media+='<img type="'+type+'" class="tweet_media_source" src="'+item.media_url+'"></img>';
				});
			}
			
			if(item.entities.urls && item.entities.urls.length){
				item.entities.urls.forEach(function(item){
					urls+='<a target="_blank" href="'+item.url+'">'+item.url+'</a><br>';
				});
			}
			
			if(item.place){
				place = item.place.full_name;
			}
			
			var retweeted = '';
			
			if(item.retweeted_status){
				text = item.retweeted_status.text;
				name = item.retweeted_status.user.name;
				uname = item.retweeted_status.user.screen_name;
				profile_image_url = item.retweeted_status.user.profile_image_url;
				retweeted = item.user.name;
				favorite_count = item.retweeted_status.favorite_count;
			}
			
			$('#tweet_indicators').append('<li data-target="#myCarousel" data-slide-to="'+index+'" '+(index==0?'class="active"':'')+'></li>');
			
			$('#tweet_main_box').append(createTimelineHTML({
				retweeted : retweeted,
				name : name,
				uname : uname,
				profile_image_url : profile_image_url,
				time : time,
				text : text,
				urls : urls,
				media : media,
				retweet_count : retweet_count,
				favorite_count : favorite_count,
				place : place
			}, index));
			
		});
		
	}
	
function display_follower_tweets(screen_name){
	if(xhr_follower && xhr_follower.readyState != 4){
		xhr_follower.abort();
	}
	xhr_follower = $.post('src/getData.php', {type:4, screen_name:screen_name, count:10}, function(response){
		response = format_text(response);
		response = JSON.parse(response);
		if(response.status=='error'){
			console.error(response.data);
		}else if(response.status=='redirect'){
			window.location.href = response.data.url;
		}else{
			display_tweets(response.data);
		}
	});
}

function format_text(data){
		return data.replace(/\\n/g,"<br>")
	}

function createTimelineHTML(obj, index){
	var date = new Date(obj.time);
		var s = '<div id="carousel_data_wrapper" class="item '+(index==0?"active":"")+'">'+
					'<span class="carousel_span_supperter"></span>'+
					'<div class="carousel_after_supporter">'+
					'<div>'+
						(obj.retweeted?'<div class="retweet_indicator">'+
							'<div class="retweet_indicator_dummy"></div'+
							'><div class="retweet_indicator_text">'+obj.retweeted+' Retweeted</div>'+
						'</div>':'')+
						'<div class="tweet_main">'+
							'<div class="profile_pic_div">'+
								'<img src="'+obj.profile_image_url+'"></img>'+
							'</div'+
							'><div class="tweet_main_div">'+
								'<div class="user_info_row">'+
									'<span class="user_name">'+obj.name+'</span>'+
									' <span class="user_screen_name">@'+obj.uname+'</span>'+
									' - '+
									'<span class="tweet_time">'+date.toLocaleDateString()+' at '+date.toLocaleTimeString()+'</span>'+
								'</div>'+
								
								'<div class="tweet_text">'+
									obj.text+
								'</div>'+
								'<div class="tweet_media">'+
									obj.media+
								'</div>'+
								
								'<div class="fav_retweet">'+
									'<div class="retweet_count">'+
										'<span class="fav_retweet_text_span retweet_text_span">Retweet : </span>'+
										'<span class="fav_retweet_count_span retweet_count_span">'+obj.retweet_count+'</span>'+
									'</div>'+
									'<div class="fav_count">'+
										'<span class="fav_retweet_text_span fav_text_span">Favourite : </span>'+
										'<span class="fav_retweet_count_span fav_count_span">'+obj.favorite_count+'</span>'+
									'</div>'+
									(obj.place?'<div class="place">'+
										'<span class="fav_retweet_text_span place_text_span">Place : </span>'+
										'<span class="fav_retweet_count_span place_count_span">'+obj.place+'</span>'+
									'</div>':'')+
								'</div>'+
							'</div>'+
						'</div>'+
					'</div>'+
				'</div>'+
				'</div>';
				
	return s;
}

function createFollowerHTML(obj){
	var s = '<div>'+
				'<div class="follower_banner_div">'+
					'<img src="'+obj.profile_banner+'"></img>'+
				'</div>'+
				'<div class="follower_body_div">'+
					'<a href="#user_timeline">'+
						'<div value="'+obj.uname+'" class="follower_user_info">'+
							'<div class="follower_profile_pic_div">'+
								'<img src="'+obj.profile_pic+'"></img>'+
							'</div>'+
							'<div class="follower_name_uname">'+
								'<div class="follower_name">'+obj.name+'</div>'+
								'<div class="follower_uname">@'+obj.uname+'</div>'+
							'</div>'+
						'</div>'+
					'</a>'+
					'<div class="follower_extra">'+
						'<div class="follower_description">'+
							obj.description+
						'</div>'+
						(obj.urls?'<div class="follower_urls">'+
							obj.urls+
						'</div>':'')+
						(obj.following?'<div class="you_follow">You are following this person</div>':'')+
					'</div>'+
				'</div>'+
			'</div>';
			
	return s;
}