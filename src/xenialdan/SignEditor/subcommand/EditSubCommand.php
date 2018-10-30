<?php

namespace xenialdan\SignEditor\subcommand;

use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat;
use xenialdan\SignEditor\Loader;

class EditSubCommand extends SubCommand{

	public function canUse(CommandSender $sender){
		return ($sender instanceof Player) and $sender->hasPermission("signeditor.command.edit");
	}

	public function getUsage(){
		return "edit";
	}

	public function getName(){
		return "edit";
	}

	public function getDescription(){
		return "Tap a sign after this command to edit it";
	}

	public function getAliases(){
		return [];
	}

	/**
	 * @param CommandSender $sender
	 * @param array $args
	 * @return bool
	 */
	public function execute(CommandSender $sender, array $args){
		Loader::$editing[$sender->getName()] = true;
		$sender->sendMessage(TextFormat::GREEN . 'Now tap a sign to edit it');
		return true;
	}
}
