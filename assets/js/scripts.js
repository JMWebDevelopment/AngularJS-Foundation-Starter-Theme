jQuery(document).foundation();
/* 
These functions make sure WordPress 
and Foundation play nice together.
*/

jQuery(document).ready(function() {
    
    // Remove empty P tags created by WP inside of Accordion and Orbit
    jQuery('.accordion p:empty, .orbit p:empty').remove();
    
	 // Makes sure last grid item floats left
	jQuery('.archive-grid .columns').last().addClass( 'end' );
	
	// Adds Flex Video to YouTube and Vimeo Embeds
	jQuery('iframe[src*="youtube.com"], iframe[src*="vimeo.com"]').wrap("<div class='flex-video'/>");

	jQuery( 'a' ).click( function() {
		jQuery('html, body').animate({
			scrollTop: jQuery("html").offset().top
		}, 500);
	});

});
function changeCurrentNavItem() {
	jQuery(function () {
		var currentItem = '';
		jQuery("#theme-slug-top-menu ul li").each(function () {
			if (jQuery(this).hasClass("current-item")) {
				currentItem = jQuery(this);
			}
		});
		var pgurl = window.location.href;
		var found = false;
		jQuery("#theme-slug-top-menu ul li").each(function () {
			if (jQuery(this).find("a").attr("href") == pgurl || jQuery(this).find("a").attr("href") == '') {
				if (currentItem != '') {
					currentItem.removeClass('current-item');
				}
				jQuery(this).addClass("current-item");
				currentItem = jQuery(this);
				found = true;
			}
			if (found == false) {
				if (currentItem != '') {
					currentItem.removeClass('current-item');
				}
			}
		});
	});
}
changeCurrentNavItem();

function getCommentById( commentID, comments_list ) {
	for ( j = 0; j < comments_list.length; j++ ) {
		if ( comments_list[j].comment_ID == commentID ) {
			return comments_list[j];
		}
	}
}
function getCommentDepth( theComment, comments_list ) {
	var depthLevel = 0;
	while ( theComment.comment_parent > 0 ) {
		theComment = getCommentById( theComment.comment_parent, comments_list );
		depthLevel++;
	}
	return depthLevel;
}
function arrangeComments( commentsList ) {
	var maxDepth = 0;
	for ( i = commentsList.length - 1; i >= 0; i-- ) {
		if ( commentsList[i].comment_approved != 1 ) {
			commentsList.splice( i, 1 );
		}
	}
	for ( i = 0; i < commentsList.length; i += 1 ) {
		commentsList[i].comment_children = [];
		var date = commentsList[i].comment_date.split(" ").join("T").concat("Z");
		commentsList[i].comment_date = new Date(date);
		commentsList[i].comment_depth = getCommentDepth( commentsList[i], commentsList );
		if ( getCommentDepth( commentsList[i], commentsList ) > maxDepth ) {
			maxDepth = getCommentDepth( commentsList[i], commentsList );
		}
	}
	for ( i = maxDepth; i > 0; i-- ) {
		for ( j = 0; j < commentsList.length; j++ ) {
			if ( commentsList[j].comment_depth == i ) {
				for ( k = 0; k < commentsList.length; k++ ) {
					if ( commentsList[j].comment_parent == commentsList[k].comment_ID ) {
						commentsList[k].comment_children.push( commentsList[j] )
					}
				}
			}
		}
	}
	for ( i = commentsList.length - 1; i >= 0; i-- ) {
		if ( commentsList[i].comment_parent > 0 ) {
			commentsList.splice( i, 1 );
		}
	}

	return commentsList;
}

function replyToComment( commentParentId ) {
	commentParentId = commentParentId.split("-").pop();
	jQuery( '#parent' ).val( commentParentId );
	var replyTo = jQuery( '#comment-' + commentParentId + ' .comment-name' ).html();
	jQuery('.add-comment').html( myLocalized.translations.replying_to + replyTo + '  <span onclick="removeReply()">myLocalized.translations.remove_reply</span>' );
	jQuery('html, body').animate({
		scrollTop: jQuery(".post-comments").offset().top
	}, 500);
}
function removeReply() {
	jQuery( '#parent' ).val('0');
	jQuery('.add-comment').html( myLocalized.translations.add_comment );
}

function setPaginationLinks( page, pagedUrl, headers, totalPages ) {
    if ( page ) {
        var currentPage = page;
    } else {
        var currentPage = 1;
    }

    if ( pagedUrl ) {
        var previousUrl = pagedUrl + ( currentPage - 1 ) + '/';
        var nextUrl = pagedUrl + ( currentPage + 1 ) + '/';
        jQuery( 'a#previous-posts' ).attr( 'href', previousUrl );
        jQuery( 'a#next-posts' ).attr( 'href', nextUrl );
        jQuery( 'a#previous-posts' ).click( function() {
            jQuery('html, body').animate({
                scrollTop: jQuery("html").offset().top
            }, 500);
        });
        jQuery( 'a#next-posts' ).click( function() {
            jQuery('html, body').animate({
                scrollTop: jQuery("html").offset().top
            }, 500);
        });
    }

    totalPages = headers('X-WP-TotalPages');

    if ( currentPage == 1 ) {
        jQuery( 'a#previous-posts' ).css( 'visibility', 'hidden' );
    }
    if ( currentPage == totalPages ) {
        jQuery( 'a#next-posts' ).css( 'visibility', 'hidden' );
    }
}

function getArchiveUrl( archiveType, endpoints, page  ) {
	var url = '';
	var pagedUrl = '';
	if ( archiveType == 'Author' ) {
        url = myLocalized.api_url + 'users?slug=' + endpoints[0];
        pagedUrl = myLocalized.site_url + 'author/' + endpoints[0] + '/page/';
    } else if ( archiveType == 'Category' ) {
        url = myLocalized.api_url + 'categories?slug=' + endpoints[0];
        pagedUrl = myLocalized.site_url + 'category/' + endpoints[0] + '/page/';
    } else if ( archiveType == 'Search' ) {
        if ( page > 1 ) {
            url = myLocalized.api_url + 'posts?search=' + endpoints[0] + '&page=' + page;
        } else {
            url = myLocalized.api_url + 'posts?search=' + endpoints[0];
        }
        pagedUrl = myLocalized.site_url + 'search/' + endpoints[0] + '/page/';
    } else if ( archiveType == 'Tag' ) {
        url = myLocalized.api_url + 'tags?slug=' + endpoints[0];
        pagedUrl = myLocalized.site_url + 'tag/' + endpoints[0] + '/page/';
    } else if ( archiveType == 'Year' ) {
        url = myLocalized.api_url + 'posts?year=' + endpoints[0];
        pagedUrl = myLocalized.site_url + '' + endpoints[0] + '/page/';
    } else if ( archiveType == 'Month' ) {
        url = myLocalized.api_url + 'posts?year=' + endpoints[0] + '&monthnum=' + endpoints[1];
        pagedUrl = myLocalized.site_url + '' + endpoints[0] + '/' + endpoints[1] + '/page/';
    } else if ( archiveType == 'Day' ) {
        url = myLocalized.api_url + 'posts?year=' + endpoints[0] + '&monthnum=' + endpoints[1] + '&day=' + endpoints[2];
        pagedUrl = myLocalized.site_url + '' + endpoints[0] + '/' + endpoints[1] + '/' + endpoints[2] + '/page/';
    }
    return [url, pagedUrl];
}

function getArchivePosts( archiveType, page, endpoint, endpointId ) {
	var url = '';
    if ( archiveType == 'Category' ) {
        if ( page > 1 ) {
            url = myLocalized.api_url + 'posts?categories=' + endpointId + '&page=' + page;
        } else {
            url = myLocalized.api_url + 'posts?categories=' + endpointId;
        }
    } else if ( archiveType == 'Tag' ) {
        if ( page > 1 ) {
            url = myLocalized.api_url + 'posts?tags=' + endpointId + '&page=' + page;
        } else {
            url = myLocalized.api_url + 'posts?tags=' + endpointId;
        }
    } else if ( archiveType == 'Author' ) {
        if ( page > 1 ) {
            url = myLocalized.api_url + 'posts?author=' + endpointId + '&page=' + page;
        } else {
            url = myLocalized.api_url + 'posts?author=' + endpointId;
        }
	}

    return url;
}

function getMonthName( month ) {
	month = month - 1;
	return myLocalized.months[month];
}

(function() {
	angular.module('myapp', ['ui.router', 'ngResource'])
		.factory('Comments',function($resource){
			return $resource(myLocalized.api_url+':ID/comments',{
				ID:'@id'
			},{
				'update':{method:'PUT'},
				'save':{
					method:'POST',
					headers: {
						'X-WP-Nonce': myLocalized.nonce
					}
				}
			});
		})
		.controller('Home', ['$scope', '$http', '$stateParams', function ($scope, $http) {
			$scope.translations = myLocalized.translations;
			$http({
				url: myLocalized.api_url + 'posts/',
				cache: true
			}).success(function (res) {
				$scope.posts = res;
				document.querySelector('title').innerHTML = myLocalized.site_title + ' | ' + myLocalized.site_description;
				changeCurrentNavItem();
			});
		}])
		.controller('SinglePost', ['$scope', '$http', '$stateParams', 'Comments', function ($scope, $http, $stateParams, Comments) {
            $scope.translations = myLocalized.translations;
			$http.get(myLocalized.api_url + 'posts?slug=' + $stateParams.slug + '&_embed').success(function(res){
				$scope.post = res[0];
				$scope.post.comments = arrangeComments( $scope.post.comments );
				$scope.numComments = $scope.post.comments.length;
				$scope.loggedIn = myLocalized.logged_in;
				if ( $scope.loggedIn == true ) {
					$scope.currentUser = myLocalized.logged_in_user;
				}
				document.querySelector('title').innerHTML = res[0].title.rendered + ' | ' + myLocalized.site_title;
				changeCurrentNavItem();
			});
			$scope.savecomment = function(){
				$scope.openComment = {};
				$scope.openComment.author_name = jQuery('#name').val();
				$scope.openComment.author_email = jQuery('#email').val();
				$scope.openComment.parent = jQuery('#parent').val();
				$scope.openComment.content = jQuery('#comment-content').val();
				$scope.openComment.post = $scope.post.id;
				jQuery('#comment-content').val('');
				if ( $scope.loggedIn == false ) {
					jQuery('#name').val('');
					jQuery('#email').val('');
				}
				Comments.save($scope.openComment, function(res){
					if( res.id ) {
						$scope.openComment = {};
						$scope.openComment.post = $scope.post.id;
					}
				});
			}
		}])
		.controller('Page', ['$scope', '$http', '$stateParams', function ($scope, $http, $stateParams) {
            $scope.translations = myLocalized.translations;
			$http.get( myLocalized.api_url + 'pages?slug=' + $stateParams.slug ).success(function(res){
				$scope.post = res[0];
				document.querySelector('title').innerHTML = res[0].title.rendered + ' | ' + myLocalized.site_title
				changeCurrentNavItem();
			});
		}])
		.controller('Archive', ['$scope', '$http', '$stateParams', function ($scope, $http, $stateParams) {
            $scope.translations = myLocalized.translations;
			var url = '';
			var pagedUrl = '';
			if ( $stateParams.archiveType == 'Day' || $stateParams.archiveType == 'Month' ) {
				var endpoints = [];
				if ( $stateParams.archiveType == 'Day' ) {
					endpoints = [ $stateParams.year, $stateParams.month, $stateParams.endpoint ];
				} else {
                    endpoints = [ $stateParams.year, $stateParams.endpoint ];
				}
			} else {
				endpoints = [ $stateParams.endpoint ];
			}
			var urls = getArchiveUrl( $stateParams.archiveType, endpoints, $stateParams.page  );
			url = urls[0];
			pagedUrl = urls[1];
			if ( $stateParams.archiveType != 'Search' && $stateParams.archiveType != 'Year' && $stateParams.archiveType != 'Month' && $stateParams.archiveType != 'Day' ) {
				$http.get(url).success(function (res) {
					$scope.term = res[0];
					$scope.archiveType = $stateParams.archiveType;
					document.querySelector('title').innerHTML = $scope.term.name + ' | ' + myLocalized.site_title;
					if ( $stateParams.archiveType == 'Author' ) {
                        $scope.archiveTitle = 'Posts by: ' + $scope.term.name;
					} else {
                        $scope.archiveTitle = $scope.term.name;
                    }
					changeCurrentNavItem();
					var url = getArchivePosts( $stateParams.archiveType, $stateParams.page, endpoints[0], $scope.term.id );
                    $http.get(url).success(function (res, status, headers) {
						$scope.posts = res;
						$scope.totalPages = headers( 'X-WP-Total' );
						setPaginationLinks( $stateParams.page, pagedUrl, headers, $scope.totalPages );
                    });

				});
			} else {
				$http.get(url).success(function (res, status, headers) {
					$scope.posts = res;
					if ( $stateParams.archiveType == 'Search' ) {
                        document.querySelector('title').innerHTML = endpoints[0] + ' | ' + myLocalized.site_title;
                        $scope.archiveTitle = 'Search: ' + endpoints[0];
                    } else if ( $stateParams.archiveType == 'Year' ) {
                        document.querySelector('title').innerHTML = endpoints[0] + ' | ' + myLocalized.site_title;
                        $scope.archiveTitle = endpoints[0];
					} else if ( $stateParams.archiveType == 'Month' ) {
                        document.querySelector('title').innerHTML = getMonthName( endpoints[1] ) + ' ' + endpoints[0] + ' | ' + myLocalized.site_title;
                        $scope.archiveTitle = getMonthName( endpoints[1] ) + ' ' + endpoints[0];
                    } else {
                        document.querySelector('title').innerHTML = getMonthName (endpoints[1] ) + ' ' + endpoints[2] + ', ' + endpoints[0] + ' | ' + myLocalized.site_title;
                        $scope.archiveTitle = getMonthName( endpoints[1] ) + ' ' + endpoints[2] + ', ' + endpoints[0];
					}
                    setPaginationLinks( $stateParams.page, pagedUrl, headers, $scope.totalPages );
				});
			}
		}])
		.controller('NotFound', ['$scope', '$http', '$stateParams', function ($scope, $http, $stateParams) {
            $scope.translations = myLocalized.translations;
		}])
		.config([ '$stateProvider', '$urlRouterProvider', '$locationProvider', function ($stateProvider, $urlRouterProvider, $locationProvider) {
			$stateProvider
				.state('Home', {
					url: '/',
					controller: 'Home',
					templateUrl: myLocalized.partials + 'main.html'
				})
				.state('Category', {
					url: '/category/:endpoint/',
					controller: 'Archive',
					params :{
						archiveType: 'Category'
					},
					templateUrl: myLocalized.partials + 'archive.html',
				})
				.state('CategoryPaged', {
					url: '/category/:endpoint/page/{page:int}/',
					controller: 'Archive',
					params :{
						archiveType: 'Category',
					},
					templateUrl: myLocalized.partials + 'archive.html',
				})
				.state('Author', {
					url: '/author/:endpoint/',
					controller: 'Archive',
					params :{
						archiveType: 'Author'
					},
					templateUrl: myLocalized.partials + 'archive.html',
				})
                .state('AuthorPaged', {
                    url: '/author/:endpoint/page/{page:int}/',
                    controller: 'Archive',
                    params :{
                        archiveType: 'Author',
                    },
                    templateUrl: myLocalized.partials + 'archive.html',
                })
				.state('Tag', {
					url: '/tag/:endpoint/',
					controller: 'Archive',
					params :{
						archiveType: 'Tag'
					},
					templateUrl: myLocalized.partials + 'archive.html',
				})
                .state('TagPaged', {
                    url: '/tag/:endpoint/page/{page:int}/',
                    controller: 'Archive',
                    params :{
                        archiveType: 'Tag'
                    },
                    templateUrl: myLocalized.partials + 'archive.html',
                })
				.state('Search', {
					url: '/search/:endpoint',
					controller: 'Archive',
					params :{
						archiveType: 'Search'
					},
					templateUrl: myLocalized.partials + 'archive.html',
				})
                .state('SearchPaged', {
                    url: '/search/:endpoint/page/{page:int}/',
                    controller: 'Archive',
                    params :{
                        archiveType: 'Search'
                    },
                    templateUrl: myLocalized.partials + 'archive.html',
                })
				.state('SinglePost', {
					url: '/{year:int}/{month:int}/{day:int}/:slug/',
					controller: 'SinglePost',
					templateUrl: myLocalized.partials + 'single.html'
				})
				.state('ArchiveYear', {
					url: '/{endpoint:int}/',
					controller: 'Archive',
					params :{
						archiveType: 'Year'
					},
					templateUrl: myLocalized.partials + 'archive.html',
				})
                .state('ArchiveYearPaged', {
                    url: '/{endpoint:int}/page/{page:int}/',
                    controller: 'Archive',
                    params :{
                        archiveType: 'Year'
                    },
                    templateUrl: myLocalized.partials + 'archive.html',
                })
				.state('ArchiveMonth', {
					url: '/{year:int}/{endpoint:int}/',
					controller: 'Archive',
					params :{
						archiveType: 'Month'
					},
					templateUrl: myLocalized.partials + 'archive.html',
				})
                .state('ArchiveMonthPaged', {
                    url: '/{year:int}/{endpoint:int}/page/{page:int}/',
                    controller: 'Archive',
                    params :{
                        archiveType: 'Month'
                    },
                    templateUrl: myLocalized.partials + 'archive.html',
                })
				.state('ArchiveDay', {
					url: '/{year:int}/{month:int}/{endpoint:int}/',
					controller: 'Archive',
					params :{
						archiveType: 'Day'
					},
					templateUrl: myLocalized.partials + 'archive.html',
				})
                .state('ArchiveDayPaged', {
                    url: '/{year:int}/{month:int}/{endpoint:int}/page/{page:int}/',
                    controller: 'Archive',
                    params :{
                        archiveType: 'Day'
                    },
                    templateUrl: myLocalized.partials + 'archive.html',
                })
                .state('Page', {
                    url: '/{slug:string}/',
                    controller: 'Page',
                    templateUrl: myLocalized.partials + 'page.html'
                })
				.state('NotFound', {
					url: '*path',
					templateUrl: myLocalized.partials + '404.html',
					controller: 'NotFound'
				});

			//Enable pretty permalinks, sans the #
			$locationProvider.html5Mode(true);
		}])
		.directive('collection', function () {
			return {
				restrict: "E",
				replace: true,
				scope: {
					collection: '='
				},
				template: "<ul><member ng-repeat='member in collection' member='member'></member></ul>"
			}
		})

		.directive('member', function ($compile) {
			return {
				restrict: "E",
				replace: true,
				scope: {
					member: '='
				},
				templateUrl: myLocalized.partials + 'comments.html',
				link: function (scope, element, attrs) {
					var collectionSt = '<collection collection="member.comment_children"></collection>';
					if (angular.isArray(scope.member.comment_children)) {
						$compile(collectionSt)(scope, function(cloned, scope)   {
							element.append(cloned);
						});
					}
				}
			}
		})
		.filter('unsafe', function($sce) {
			return function(val) {
				return $sce.trustAsHtml(val);
			};
		});
})();