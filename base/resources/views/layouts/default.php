<!DOCTYPE html>
<html>
<head>
<base href="<?=$this->getWebRoot()?>" />
<title><?=$this->getTitle() ?? $this->project->module?></title>
<?=$this->renderMetaTags()?>
<?=$this->renderLinkTags()?>
<?=$this->renderScriptTags()?>
</head>
<body>
<?=$this->insertContent()?>
<footer style="font-family: sans-serif; position: fixed; bottom: 3px; right: 3px; font-size: 10px; color: #aaa; text-align: center; font-variant: small-caps">
Built with <a href="http://vendimia.in/" style="color: #888;" target="_blank">Vendimia Framework</a>
</footer>
</body>
</html>