<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="2.0" 
    xmlns:html="http://www.w3.org/TR/REC-html40"
    xmlns:sitemap="http://www.sitemaps.org/schemas/sitemap/0.9"
    xmlns:video="http://www.google.com/schemas/sitemap-video/1.0"
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:output method="html" version="1.0" encoding="UTF-8" indent="yes"/>
	
<xsl:template match="/">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>Video Sitemap</title>
		<meta http-equiv="Content-Type" content="text/xml; charset=UTF-8" />
		<style type="text/css">
			body {
				background: #fff;
				margin: 0;
				padding: 0; }
			
			body, td {
				font: 11px Verdana, "Lucida Grande", "Lucida Sans Unicode", Tahoma, sans-serif; }
			
			#vfx_wrapper {
				width: 100%;
				background: #fff;
				clear: both;
				margin: 20px 20px;
				padding: 0;
				max-width: 840px; }
			
			h1 {
				color: #000;
				clear: both;
				font: 18px  "Lucida Grande", "Lucida Sans Unicode", Tahoma, Verdana, sans-serif;
				margin: 5px 0 0;
				padding: 0;
				padding-bottom: 7px;
				padding-right: 400px; }
			
			#vfx_note {
			 text-align: right; }
			
			#vfx_title {
    	 font: 14px Verdana, "Lucida Grande", "Lucida Sans Unicode", Tahoma, sans-serif; }
			
			table {
				border-color: #ccc;
				border-width: 1px;
				border-style: solid;
				border-collapse: collapse;
				width: 100%;
				clear: both;
				margin: 0; }
				
			td {
				border-bottom-width: 1px;
				border-bottom-style: dotted;
				border-bottom-color: #ccc;
				padding: 6px 15px 10px 10px;
				line-height: 20px;
				color: #444; }
		      
      table.inset {
				border-color: #fff;
				border-width: 0px;
				border-style: none;
				border-collapse: collapse;
				width: 100%;
				clear: both;
				margin: 0; }
		
      td.inset {
				border-bottom-style: none;
				padding: 2px 15px 4px 10px;
				line-height: 20px;
				color: #444; }

    		
			img {
				padding: 2px;
				border-style: none; }
				
			p {
      text-align:center;
      }
			
			a {
				color: #2583ad;
				text-decoration: none; }

			a:hover {
				color: #d54e21; }
		</style>
	</head>
	<body>
		<div id="vfx_wrapper">
		  <table>
		  <tr><td><h1>Video Sitemap</h1></td>
			<td><div id="vfx_note">
			</div></td>
			</tr></table>		  			
			<div id="content">
				<table>
				<tbody>
					<xsl:for-each select="sitemap:urlset/sitemap:url">
						<tr>
							<xsl:if test="position() mod 2 = 1">
								<xsl:attribute name="class">odd</xsl:attribute>
							</xsl:if>

							<td>
								<xsl:variable name="mythumb">
									<xsl:value-of select="video:video/video:thumbnail_loc"/>
								</xsl:variable>
								
								<xsl:variable name="myvlink">
									<xsl:value-of select="sitemap:loc"/>
								</xsl:variable>
								
								<a href="{$myvlink}"><img src="{$mythumb}" width="120" height="90" /></a>
							</td>
							
							<td>
								<table class="inset">
								<tr>
								<td class="inset">
								<div id ="vfx_title">
                <xsl:variable name="mytlink">
									<xsl:value-of select="sitemap:loc"/>
								</xsl:variable>
                <a href="{$mytlink}">
									<xsl:value-of select="video:video/video:title"/>
								</a>
								</div>
							</td>
							</tr>
              
              <tr>
							<td class="inset">
								<xsl:variable name="desc">
									<xsl:value-of select="video:video/video:description"/>
								</xsl:variable>      
								<xsl:choose>
									<xsl:when test="string-length($desc) &lt; 200">
										<xsl:value-of select="$desc"/>
									</xsl:when>
									<xsl:otherwise>
										<xsl:value-of select="concat(substring($desc,1,200),' ...')"/>
									</xsl:otherwise>
								</xsl:choose>
							</td>
							</tr>
							</table>
							</td>
						</tr>
					</xsl:for-each>
					</tbody>
				</table>
		</div>
		<p><a href="http://www.videoflow.tv/" title="VideoFlow: Multimedia System for Joomla and Facebook">Generated by VideoFlow</a></p>
		</div>
	</body>
</html>
</xsl:template>

</xsl:stylesheet>