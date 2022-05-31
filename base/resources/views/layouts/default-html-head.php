<!DOCTYPE html>
<html>
<head>
<base href="<?=$this->getWebRoot()?>" />
<title><?=$this->getTitle() ?? $this->project->module?></title>
<?=$this->renderMetaTags()?>
<?=$this->renderLinkTags()?>
<?=$this->renderScriptTags()?>
</head>