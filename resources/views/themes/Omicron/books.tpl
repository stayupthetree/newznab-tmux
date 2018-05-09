<div class="header" xmlns="http://www.w3.org/1999/html" xmlns="http://www.w3.org/1999/html"
	 xmlns="http://www.w3.org/1999/html">
	<div class="breadcrumb-wrapper">
		<ol class="breadcrumb">
			<li><a href="{$smarty.const.WWW_TOP}{$site->home_link}">Home</a></li>
			/ {$catname|escape:"htmlall"}
		</ol>
	</div>
</div>
<div class="well well-sm">
	{include file='search-filter.tpl'}
</div>
<form id="nzb_multi_operations_form" action="get">
	<div class="box-body"
	<div class="row">
		<div class="box col-md-12">
			<div class="box-content">
				<div class="row">
					<div class="col-lg-12 col-sm-12 col-xs-12">
						<div class="panel panel-default">
							<div class="panel-body pagination2">
								<div class="row">
									<div class="col-md-8">
										<div class="nzb_multi_operations">
											View: <strong>Covers</strong> | <a
													href="{$smarty.const.WWW_TOP}/browse/books/{$categorytitle}">List</a><br/>
											Check all: <input type="checkbox" class="square-all"/> <br/>
											With Selected:
											<div class="btn-group">
												<input type="button"
													   class="nzb_multi_operations_download btn btn-sm btn-success"
													   value="Download NZBs"/>
												<input type="button"
													   class="nzb_multi_operations_cart btn btn-sm btn-info"
													   value="Send to my Download Basket"/>
												{if isset($sabintegrated) && $sabintegrated !=""}
													<input type="button"
														   class="nzb_multi_operations_sab btn btn-sm btn-primary"
														   value="Send to Queue"/>
												{/if}
												{if isset($isadmin)}
													<input type="button"
														   class="nzb_multi_operations_delete btn btn-sm btn-danger"
														   value="Delete"/>
												{/if}
											</div>
										</div>
									</div>
									{if    count($results) > 0}
										<div class="col-md-4">
											{$results->links()}
										</div>
									{/if}
								</div>
								<hr>
								{foreach $resultsadd as $result}
									<div class="panel panel-default">
										<div class="panel-body">
											<div class="row">
												<div class="col-md-2 small-gutter-left">
													<a title="View details"
													   href="{$smarty.const.WWW_TOP}/details/{$result.grp_release_guid}">
														<img src="{$smarty.const.WWW_TOP}/covers/book/{if $result.cover == 1}{$result.bookinfo_id}.jpg{else}{$smarty.const.WWW_ASSETS}/images/no-cover.png{/if}"
															 class="img-responsive img-rounded"
															 width="140" border="0"
															 alt="{$result.author|escape:"htmlall"} - {$result.title|escape:"htmlall"}"/>{if isset($result.grp_release_failed) && $result.grp_release_failed > 0}
														<i class="fa fa-exclamation-circle" style="color: red"
														   title="This release has failed to download for some users"></i>{/if}
													</a>
													{if isset($result.url) && $result.url != ""}<a
														class="label label-default" target="_blank"
														href="{$site->dereferrer_link}{$result.url}"
														name="amazon{$result.bookinfo_id}" title="View amazon page">
															Amazon</a>{/if}
													{if isset($mnfo[$m@index]) && $mnfo[$m@index] > 0}<a
														href="{$smarty.const.WWW_TOP}/nfo/{$result.grp_release_guid}"
														title="View Nfo" class="modal_nfo label label-default"rel="nfo">
															NFO</a>{/if}
													<a class="label label-default"
													   href="{$smarty.const.WWW_TOP}/browse/group?g={$mgrp[$m@index]}"
													   title="Browse releases in {$mgrp[$m@index]|replace:"alt.binaries":"a.b"}">Group</a>
													{if isset($result.grp_release_failed) && $result.grp_release_failed > 0}
													<span class="btn btn-hover btn-default btn-xs"><i
																class="fa fa-thumbs-o-down"></i><span
																class="badge"> {$result.grp_release_failed}
															Failed Download{if $result.grp_release_failed > 1}s{/if}</span>
														{/if}
												</div>
												<div class="col-md-10 small-gutter-left">
													<h4><a title="View details"
														   href="{$smarty.const.WWW_TOP}/details/{$result.grp_release_guid}">{$result.author|escape:"htmlall"}
															- {$result.title|escape:"htmlall"}</a></h4>
													<table>
														<tr>
															<td id="guid{$result.grp_release_guid}">
																<label>
																	<input type="checkbox"
																		   class="square"
																		   value="{$result.grp_release_guid}"
																		   id="chksingle"/>
																</label>
																<span class="label label-default">{$result.grp_release_size|fsize_format:"MB"}</span>
																<span class="label label-default">Posted {$result.grp_release_postdate|timeago}
																	ago</span>
																<br/>
																{if isset($result.review) && $result.review != ""}<span
																		class="descinitial">{$result.review|escape:"htmlall"|nl2br|magicurl|truncate:350}</span>{if $result.review|strlen > 350}
																	<a class="descmore" href="#">more...</a>
																	<span class="descfull">{$result.review|escape:"htmlall"|nl2br|magicurl}</span>{else}</span>{/if}
																	<br/>
																	<br/>
																{/if}
																{if isset($result.publisher) && $result.publisher != ""}
																	<b>Publisher:</b>
																	{$result.publisher|escape:"htmlall"}
																	<br/>
																{/if}
																{if isset($result.publishdate) && $result.publishdate != ""}
																	<b>Published:</b>
																	{$result.publishdate|date_format}
																	<br/>
																{/if}
																{if isset($result.pages) && $result.pages != ""}
																	<b>Pages:</b>
																	{$result.pages}
																	<br/>
																{/if}
																{if isset($result.isbn) && $result.isbn != ""}
																	<b>ISBN:</b>
																	{$result.isbn}
																	<br/>
																{/if}
																<div>
																	<a role="button" class="btn btn-default btn-xs"
																	   href="{$smarty.const.WWW_TOP}/getnzb?id={$result.grp_release_guid}"><i
																				class="fa fa-cloud-download"></i><span
																				class="badge"> {$result.grp_release_grabs}
																			Grab{if $result.grp_release_grabs != 1}s{/if}</span></a>
																	<a role="button" class="btn btn-default btn-xs"
																	   href="{$smarty.const.WWW_TOP}/details/{$result.grp_release_guid}/#comments"><i
																				class="fa fa-comment-o"></i><span
																				class="badge"> {$result.grp_release_comments}
																			Comment{if $result.grp_release_comments != 1}s{/if}</span></a>
																	<span
																			id="guid{$result.grp_release_guid}"
																			class="btn btn-hover btn-default btn-xs icon_cart text-muted"
																			title="Send to my Download Basket">
																	<i class="fa fa-shopping-basket"></i>
															</span>
																	{if isset($sabintegrated) && $sabintegrated !=""}
																		<span
																				id="guid{$result.grp_release_guid}"
																				class="btn btn-hover btn-default btn-xs icon_sab text-muted"
																				title="Send to my Queue">
																		<i class="fa fa-share"></i>
																</span>
																	{/if}
																	{if !empty($result.grp_release_failed)}
																		<span class="btn btn-default btn-xs"
																			  title="This release has failed to download for some users">
																	<i class="fa fa-thumbs-o-up"></i> {$result.grp_release_grabs}
																			Grab{if {$result.grp_release_grabs} != 1}s{/if}
																			/ <i class="fa fa-thumbs-o-down"></i> {$result.grp_release_failed}
																			Failed Download{if {$result.grp_release_failed} > 1}s{/if}</span>
																	{/if}
																</div>
															</td>
														</tr>
													</table>
												</div>
											</div>
										</div>
									</div>
								{/foreach}
								<div class="row">
									<div class="col-md-8">
										<form id="nzb_multi_operations_form" action="get">
											<div class="nzb_multi_operations">
												View: <strong>Covers</strong> | <a
														href="{$smarty.const.WWW_TOP}/browse/{$category->title}">List</a><br/>
												Check all: <input type="checkbox" class="square-all"/> <br/>
												With Selected:
												<div class="btn-group">
													<input type="button"
														   class="nzb_multi_operations_download btn btn-sm btn-success"
														   value="Download NZBs"/>
													<input type="button"
														   class="nzb_multi_operations_cart btn btn-sm btn-info"
														   value="Send to my Download Basket"/>
													{if isset($sabintegrated) && $sabintegrated !=""}
														<input type="button"
															   class="nzb_multi_operations_sab btn btn-sm btn-primary"
															   value="Send to Queue"/>
													{/if}
													{if isset($isadmin)}
														<input type="button"
															   class="nzb_multi_operations_delete btn btn-sm btn-danger"
															   value="Delete"/>
													{/if}
												</div>
											</div>
									</div>
									{if    count($results) > 0}
										<div class="col-md-4">
											{$results->links()}
										</div>
									{/if}
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</form>
