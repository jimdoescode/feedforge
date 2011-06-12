<h1>Welcome to the Feedforge Admin Page</h1>
<p>
	Here you can create and update feeds, add and update feed entries, as well as handle various other administrative duties.
</p>
<article class='column'>
	<div class='internal blue-border'>
		<h2>What is a feed?</h2>
		<p>
			A feed is a list of similar content. Using feeds in your templates will let you display a number of content entries that have similar attributes, like a list of blog posts.
		</p>
		<p>
			You can use the feed tag by adding the following to your template:<br/><br/>
			{ff:feed="blogs"}<br/>
			html content to display for each of my blog entries<br/>
			{/ff:feed}
		</p>
	</div>
</article>

<article class='column'>
	<div class='internal green-border'>
		<h2>What is an entry?</h2>
		<p>
			Entries are the content of feeds. In simpler terms entries are your content. Whenever you use a feed you will be displaying entries, an example would be a single blog post.
		</p>
		<p>
			Entry values are accessed within feed tags as follows:<br/><br/>
			{ff:feed="blogs"}<br/>
			&lt;h1&gt;{title}&lt;/h1&gt;<br/>
			&lt;p&gt;{body}&lt;/p&gt;<br/>
			{/ff:feed}
		</p>
	</div>
</article>
<article class='column column-last'>
	<div class='internal purple-border'>
		<h2> What are variables?</h2>
		<p>
			Variables are constant values that you wish to access in your templates. Say for instance an address which will be displayed many places and could change eventually.
		</p>
		<p>
			You can use variables in your site by putting the following in your template:<br/><br/>
			{ff:global="current-address"}
		</p>
	</div>
</article>
