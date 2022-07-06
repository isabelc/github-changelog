# Github Changelog

Github Changelog is a WordPress plugin that lets you display a changelog for a GitHub repo on your WordPress site. It also lets you display a download link for the .zip of the latest release.

The changelog displayed is actually all the release notes for all releases of a single GitHub repo. You can display this changelog anywhere on your WordPress site with a "shortcode."

Likewise, you can display the download link of the latest release with a shortcode.

## Download and Install

1. Download the plugin, [GitHub Changelog version 1.1.1](https://github.com/isabelc/github-changelog/archive/1.1.1.zip)
2. Upload the .zip file through **Plugins –> Add New –> Upload** in your WordPress dashboard.
3. Use one of the following shortcodes to display a GitHub changelog or to display a download link for the latest release of a GitHub repo.

## Display a GitHub Changelog

What is displayed as a “changelog” is actually all of the release notes for a GitHub repo.

To display a changelog, use this shortcode:

```
[github_changelog username="yourname" repo="Name-Of-Your-Repo"]
```

Replace `yourname` with your GitHub username. Replace `Name-Of-Your-Repo` with the name of your GitHub repo.

For private repos, you must add a token parameter:

```
[github_changelog username="yourname" repo="Name-Of-Your-Repo" token="yourToken"]
```
Replace `yourToken` with your own token.


## Display a Download Link For The Latest Release of a GitHub Repo

To display a download link for the latest release (.zip), use this shortcode:

```
[github_latest_release_zip username="yourname" repo="Name-Of-Your-Repo"]
```

Replace `yourname` with your GitHub username. Replace `Name-Of-Your-Repo` with the name of your GitHub repo.

By default, the link will have the anchor text: 

> Download {Name-Of-Your-Repo} {version}
For example:

>Download My-cool-repo 2.0 

You can customize the label by adding the “label” parameter to the shortcode:

```
[github_latest_release_zip username="yourname" repo="Name-Of-Your-Repo" label="Free Download"]
```

Replace `Free Download` with your custom text. Here, you can use 2 special tags:

1. Use `{name}` to show the repo’s name.
2. Use `{version}` to show the latest version.

For example, if you want the label to say “Download version 2.0”, add the label parameter like this:

```
label="Download version {version}"
```
