<div class="header">
	{assign var="catsplit" value=">"|explode:$catname}
	<h2>{$catsplit[0]} > <strong>{$catsplit[1]}</strong></h2>

	<div class="breadcrumb-wrapper">
		<ol class="breadcrumb">
			<li><a href="{$smarty.const.WWW_TOP}{$site->home_link}">Home</a></li>
			/ {$catname|escape:"htmlall"}
		</ol>
	</div>
</div>

{if $results|@count > 0}

	<form id="nzb_multi_operations_form" action="get">
		<div class="box-body"
		<div class="row">
			<div class="col-xlg-12 portlets">
				<div class="panel">
					<div class="panel-content pagination2">
						<div class="row">
							<div class="col-md-8">
								<div class="nzb_multi_operations">
									View: <strong>Covers</strong> | <a
											href="{$smarty.const.WWW_TOP}/browse?t={$category}">List</a><br/>
									With Selected:
									<div class="btn-group">
										<input type="button"
											   class="nzb_multi_operations_download btn btn-sm btn-success"
											   value="Download NZBs"/>
										<input type="button"
											   class="nzb_multi_operations_cart btn btn-sm btn-info"
											   value="Add to Cart"/>
										{if isset($sabintegrated)}
											<input type="button"
												   class="nzb_multi_operations_sab btn btn-sm btn-primary"
												   value="Send to Queue"/>
										{/if}
										{if isset($nzbgetintegrated)}
											<input type="button"
												   class="nzb_multi_operations_nzbget btn btn-sm btn-primary"
												   value="Send to NZBGet"/>
										{/if}
										{if isset($isadmin)}
											<input type="button"
												   class="nzb_multi_operations_edit btn btn-sm btn-warning"
												   value="Edit"/>
											<input type="button"
												   class="nzb_multi_operations_delete btn btn-sm btn-danger"
												   value="Delete"/>
										{/if}
									</div>
								</div>
							</div>
							<div class="col-md-4">
								{$pager}
							</div>
						</div>
						<hr>

								<table class="table table-striped table-condensed" id="coverstable">
									<thead>
									<tr>
										<th><input title="Check all" type="checkbox" class="nzb_check_all"></th>
									</tr>
									</thead>
									<tbody>
									{foreach from=$results item=result}
										<tr>
											<td style="vertical-align:top;text-align:center;width:150px;padding:10px;">
												<div>
													<a target="_blank"
													   href="{$smarty.const.WWW_TOP}/xxx/?id={$result.id}"
													   name="name{$result.id}" title="View XXX info" rel="viewxxx">
														<img src="{if $result.cover == 1}{$smarty.const.WWW_TOP}/covers/xxx/{$result.id}-cover.jpg{else}{$serverroot}templates/omicron/images/nocover.png{/if}"
															 width="130" border="0"
															 alt="{$result.title|escape:"htmlall"}">
													</a>

													<div class="relextra" style="margin-top:5px;">
														{if $result.classused == "ade"}
															<a
																	target="_blank"
																	href="{$site->dereferrer_link}{$result.directurl}"
																	name="viewade{$result.title}"
																	title="View AdultdvdEmpire page"
																	><img
																		src="{$smarty.const.WWW_TOP}/templates/nntmux/images/icons/ade.png"></a>
														{else}
															<a
																	target="_blank"
																	href="{$site->dereferrer_link}http://www.adultdvdempire.com/dvd/search?q={$result.title}"
																	name="viewade{$result.title}"
																	title="Search AdultdvdEmpire page"
																	><img
																		src="{$smarty.const.WWW_TOP}/templates/nntmux/images/icons/ade.png"></a>
														{/if}
														{if $result.classused == "hm"}
															<a
																	target="_blank"
																	href="{$site->dereferrer_link}{$result.directurl}"
																	name="viewhm{$result.title}"
																	title="View Hot Movies page"
																	><img
																		src="{$smarty.const.WWW_TOP}/templates/nntmux/images/icons/hotmovies.png"></a>
														{else}
															<a
																	target="_blank"
																	href="{$site->dereferrer_link}http://www.hotmovies.com/search.php?words={$result.title}&complete=on&search_in=video_title"
																	name="viewhm{$result.title}"
																	title="Search Hot Movies page"
																	><img
																		src="{$smarty.const.WWW_TOP}/templates/nntmux/images/icons/hotmovies.png"></a>
														{/if}
														{if $result.classused == "pop"}
															<a
																	target="_blank"
																	href="{$site->dereferrer_link}{$result.directurl}"
																	name="viewpop{$result.id}"
																	title="View Popporn page"
																	><img
																		src="{$smarty.const.WWW_TOP}/templates/nntmux/images/icons/popporn.png"></a>
														{else}
															<a
																	target="_blank"
																	href="{$site->dereferrer_link}http://www.popporn.com/results/index.cfm?v=4&g=0&searchtext={$result.title}"
																	name="viewpop{$result.id}"
																	title="Search Popporn page"
																	><img
																		src="{$smarty.const.WWW_TOP}/templates/nntmux/images/icons/popporn.png"></a>
														{/if}
														<a
																target="_blank"
																href="{$site->dereferrer_link}http://www.iafd.com/results.asp?searchtype=title&searchstring={$result.title}"
																name="viewiafd{$result.title}"
																title="Search Internet Adult Film Database"
																><img
																	src="{$smarty.const.WWW_TOP}/templates/nntmux/images/icons/iafd.png"></a>
													</div>
													<hr>
													<div>
														<a
																class="label label-info"
																href="{$smarty.const.WWW_TOP}/search/{$result.title|escape:"url"}?t=2000"
																title="View similar nzbs"
																>Similar</a>
													</div>
												</div>
											</td>
											<td colspan="3" class="left">
												<h2>
													<a
															title="{$result.title|stripslashes|escape:"htmlall"}"
															href="{$smarty.const.WWW_TOP}/xxx?id={$result.id}">{$result.title|stripslashes|escape:"htmlall"}
													</a>
												</h2>
												{if $result.tagline != ''}
													<b>{$result.tagline|stripslashes}</b>
													<br>
												{/if}
												{if $result.plot != ''}
													<span class="descinitial">{$result.plot|escape:"htmlall"|nl2br|magicurl|truncate:350}</span>
													<br>
												{/if}
												<br>
												{if $result.genre != ''}
													<b>Genre:</b>
													{$result.genre|stripslashes}
													<br>
												{/if}
												{if $result.director != ''}
													<b>Director:</b>
													{$result.director}
													<br>
												{/if}
												{if $result.actors != ''}
													<b>Starring:</b>
													{$result.actors}
													<br>
												{/if}
												<br>

												<div class="relextra">
													<table class="table table-condensed table-hover data">
														{assign var="msplits" value=","|explode:$result.grp_release_id}
														{assign var="mguid" value=","|explode:$result.grp_release_guid}
														{assign var="mnfo" value=","|explode:$result.grp_release_nfoid}
														{assign var="mgrp" value=","|explode:$result.grp_release_grpname}
														{assign var="mname" value="#"|explode:$result.grp_release_name}
														{assign var="mpostdate" value=","|explode:$result.grp_release_postdate}
														{assign var="msize" value=","|explode:$result.grp_release_size}
														{assign var="mtotalparts" value=","|explode:$result.grp_release_totalparts}
														{assign var="mcomments" value=","|explode:$result.grp_release_comments}
														{assign var="mgrabs" value=","|explode:$result.grp_release_grabs}
														{assign var="mpass" value=","|explode:$result.grp_release_password}
														{assign var="minnerfiles" value=","|explode:$result.grp_rarinnerfilecount}
														{assign var="mhaspreview" value=","|explode:$result.grp_haspreview}
														<tbody>
														{foreach from=$msplits item=m}
															<tr id="guid{$mguid[$m@index]}"
																{if $m@index > 1}class="mlextra"
																style="display:none;"{/if}>
																<td style="width: 27px;">
																	<input type="checkbox" class="nzb_check"
																		   value="{$mguid[$m@index]}"
																		   id="{$mguid[$m@index]}"/>
																</td>
																<td class="name">
																	<a href="{$smarty.const.WWW_TOP}/details/{$mguid[$m@index]}/{$mname[$m@index]|escape:"htmlall"}">
																		<b>{$mname[$m@index]|escape:"htmlall"}</b>
																	</a>
																	<br>

																	<div class="resextra">
																		<div class="pull-left">
																			<i class="icon-calendar"></i>
																			Posted {$mpostdate[$m@index]|timeago} |
																			<i class="icon-hdd"></i> {$msize[$m@index]|fsize_format:"MB"}
																			|
																			<i class="icon-file"></i>
																			<a
																					title="View file list"
																					href="{$smarty.const.WWW_TOP}/filelist/{$mguid[$m@index]}">{$mtotalparts[$m@index]}
																				files
																			</a> |
																			<i class="icon-comments"></i>
																			<a
																					title="View comments for {$mname[$m@index]|escape:"htmlall"}"
																					href="{$smarty.const.WWW_TOP}/details/{$mguid[$m@index]}/{$mname[$m@index]|escape:"htmlall"}#comments">{$mcomments[$m@index]}
																				cmt{if $mcomments[$m@index] != 1}s{/if}
																			</a> |
																			<i class="icon-download"></i> {$mgrabs[$m@index]}
																			grab{if $mgrabs[$m@index] != 1}s{/if}
																		</div>
																		<div class="pull-right">
																			{if $mnfo[$m@index] > 0}
																				<span class="label label-default">
															<a
																	href="{$smarty.const.WWW_TOP}/nfo/{$mguid[$m@index]}"
																	title="View Nfo"
																	class="modal_nfo"
																	rel="nfo"
																	><i class="icon-info-sign"></i></a></span
																						>
																			{/if}
																			{if $mhaspreview[$m@index] == 1 && $userdata.canpreview == 1}
																				<span class="label label-default">
																<a
																		href="{$smarty.const.WWW_TOP}/covers/preview/{$mguid[$m@index]}_thumb.jpg"
																		name="name{$mguid[$m@index]}"
																		title="Screenshot of {$mname[$m@index]|escape:"htmlall"}"
																		class="modal_prev"
																		rel="preview"
																		><i class="icon-camera"></i></a></span
																						>
																			{/if}
																			{if $minnerfiles[$m@index] > 0}
																				<span class="label label-default">
																	<a
																			href="#" onclick="return false;"
																			class="mediainfo"
																			title="{$mguid[$m@index]}"
																			><i class="icon-list-alt"></i></a></span
																						>
																			{/if}
																			<span class="label label-default">
																		<a
																				href="{$smarty.const.WWW_TOP}/browse?g={$mgrp[$m@index]}"
																				title="Browse releases in {$mgrp[$m@index]}"
																				><i class="icon-share-alt"></i></a></span
																					>
																			{if $mpass[$m@index] == 1}
																				<span class="icon-stack"
																					  title="Potentially Passworded"><i
																							class="icon-check-empty icon-stack-base"></i><i
																							class="icon-unlock-alt"></i></span>
																			{elseif $mpass[$m@index] == 2}
																				<span class="icon-stack"
																					  title="Broken Post"><i
																							class="icon-check-empty icon-stack-base"></i><i
																							class="icon-unlink"></i></span>
																			{elseif $mpass[$m@index] == 10}
																				<span class="icon-stack"
																					  title="Passworded Archive"><i
																							class="icon-check-empty icon-stack-base"></i><i
																							class="icon-lock"></i></span>
																			{/if}
																		</div>
																	</div>
																</td>
																<td class="icons" style="width:90px;">
																	<div class="icon icon_nzb float-right">
																		<a
																				title="Download Nzb"
																				href="{$smarty.const.WWW_TOP}/getnzb/{$mguid[$m@index]}/{$mname[$m@index]|escape:"htmlall"}"
																				></a>
																	</div>
																	{if isset($sabintegrated)}
																		<div class="icon icon_sab float-right"
																			 title="Send to my Queue"></div>
																	{/if}
																	<div class="icon icon_cart float-right"
																		 title="Add to Cart"></div>
																	<br>
																	{*s{if $isadmin || $ismod}
																	<a class="label label-warning" href="{$smarty.const.WWW_TOP}/admin/release-edit.php?id={$result.id}&amp;from={$smarty.server.REQUEST_URI|escape:"url"}" title="Edit Release">Edit</a>
																	<a class="label confirm_action label-danger" href="{$smarty.const.WWW_TOP}/admin/release-delete.php?id={$result.id}&amp;from={$smarty.server.REQUEST_URI|escape:"url"}" title="Delete Release">Delete</a>
																	{/if}*}
																</td>
															</tr>
															{if $m@index == 1 && $m@total > 2}
																<tr>
																	<td colspan="5">
																		<a class="mlmore" href="#">{$m@total-2}
																			more...</a>
																	</td>
																</tr>
															{/if}
														{/foreach}
														</tbody>
													</table>
												</div>
											</td>
										</tr>
									{/foreach}
									</tbody>
								</table>
								<br/>

						<hr>
						<div class="row">
							<div class="col-md-8">
								<div class="nzb_multi_operations">
									View: <strong>Covers</strong> | <a
											href="{$smarty.const.WWW_TOP}/browse?t={$category}">List</a><br/>
									With Selected:
									<div class="btn-group">
										<input type="button"
											   class="nzb_multi_operations_download btn btn-sm btn-success"
											   value="Download NZBs"/>
										<input type="button"
											   class="nzb_multi_operations_cart btn btn-sm btn-info"
											   value="Add to Cart"/>
										{if isset($sabintegrated)}
											<input type="button"
												   class="nzb_multi_operations_sab btn btn-sm btn-primary"
												   value="Send to Queue"/>
										{/if}
										{if isset($nzbgetintegrated)}
											<input type="button"
												   class="nzb_multi_operations_nzbget btn btn-sm btn-primary"
												   value="Send to NZBGet"/>
										{/if}
										{if isset($isadmin)}
											<input type="button"
												   class="nzb_multi_operations_edit btn btn-sm btn-warning"
												   value="Edit"/>
											<input type="button"
												   class="nzb_multi_operations_delete btn btn-sm btn-danger"
												   value="Delete"/>
										{/if}
									</div>
								</div>
							</div>
							<div class="col-md-4">
								{$pager}
							</div>
						</div>
							{else}
							<div class="alert alert-link" style="vertical-align:middle;">
								<button type="button" class="close" data-dismiss="alert">&times;</button>
								<div class="pull-left" style="margin-right: 15px;">
									<h2 style="margin-top: 7px;"> ಠ_ಠ </h2>
								</div>
								<p>No movie releases have XXX covers.
									<br>This might mean the Administrator's has file permission issues, or he has
									disabled looking up XXX
									covers.
									<br>This could also mean there are no movie releases.
									<br>Please try looking in the
									<a href="{$smarty.const.WWW_TOP}/browse?t={$category}"
									   style="text-decoration:underline;"
											>list view</a>, which does not require XXX covers.
								</p>
							</div>
							{/if}

						</div>
					</div>
				</div>
			</div>
	</form>