<?php

namespace WarpXYZ;

use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use pocketmine\utils\Config;
use pocketmine\event\Listener;
use pocketmine\level\Position;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\network\protocol\Info as ProtocolInfo;
use pocketmine\Entity\entity;
use pocketmine\math\Vector3;
use pocketmine\math\Vector2;
use pocketmine\Player;
use pocketmine\level\Level;
use pocketmine\level\Explosion;
use pocketmine\utils\TextFormat;
use pocketmine\utils\MainLogger;
use pocketmine\plugin\Plugin;
use pocketmine\block\Block;

class Main extends PluginBase implements Listener{

  public function onEnable(){
    $this->getServer()->getPluginManager()->registerEvents($this,$this);
    if(!file_exists($this->getDataFolder())){
        mkdir($this->getDataFolder(), 0744, true);
    }
    $this->warp = new Config($this->getDataFolder() . "Warp.yml", Config::YAML);
    $this->warps = new Config($this->getDataFolder() . "Warp2.yml", Config::YAML);
    $this->name = new Config($this->getDataFolder() . "WarpName.yml", Config::YAML);
  }

  public function onCommand(CommandSender $sender, Command $command, $label, array $args){
    if(strtolower($command->getName()) == "xwarp"){
      if(!isset($args[0])) return false;
      switch($args[0]){
       case "help":
        $sender->sendMessage("§a[About WARPXYZPLUGIN]\n§b/xwarp one §f一つ目の地点。\n§b/xwarp two <ポイント名> §fポイントを設定。\n§b/xwarp remove <ポイント名>§fワープポイントを削除");
        break;

       case "one":
       $x = (Int)$sender->getX();
       $y = (Int)$sender->getY();
       $z = (Int)$sender->getZ();
       $level = $sender->getLevel();
       $l = $level->getName();
       $this->warp1[$sender->getName()] = "".$x.":".$y.":".$z.":".$l."";
       $sender->sendMessage("§bXYZWARP >> ".$this->warp1[$sender->getName()]."にポイントを指定しました。");
      return true;
        break;
        case "two":
        if(!isset($args[1])) return false;
        $x = (Int)$sender->getX();
       $y = (Int)$sender->getY();
       $z = (Int)$sender->getZ();
       $level = $sender->getLevel();
       $l = $level->getName();
       $this->warp2[$sender->getName()] = "".$x.":".$y.":".$z.":".$l."";
       $this->warp->set($this->warp1[$sender->getName()], $this->warp2[$sender->getName()]);
       $this->warp->save();
       $this->warps->set($this->warp2[$sender->getName()], $this->warp1[$sender->getName()]);
       $this->warps->save();
       $this->name->set($args[1], $this->warp1[$sender->getName()]);
       $this->name->save();
       $sender->sendMessage("§bXYZWARP >> ".$this->warp2[$sender->getName()]."にポイント§f".$args[1]."§bを指定しました。");
        break;
        return true;
        case "remove":
        if(!isset($args[1])) return false;
        if($this->name->exists($args[1])){
          $na = $this->name->get($args[1]);
          $na1 = $this->warp->get($na);
          $this->warps->remove($na1);
          $this->warps->save();
          $this->warp->remove($na);
          $this->warp->save();
          $this->name->remove($args[1]);
          $this->name->save();
          $sender->sendMessage("§bXYZWARP >> ".$args[1]."を削除しました。");
        }else{
          $sender->sendMessage("§bXYZWARP >> ".$args[1]."は存在しません！");
        }
      return true;
        break;
    }
  }
}
  public function onMove(PlayerMoveEvent $e){
    $p = $e->getPlayer();
    if($p->isSneaking()){
    $name = $p->getName();
    $x = (Int)$p->getX();
    $y = (Int)$p->getY();
    $z = (Int)$p->getZ();
    $level = $p->getLevel();
    $l = $level->getName();
    $a = "".$x.":".$y.":".$z.":".$l."";
    if($this->warp->exists($a)){
      $b = $this->warp->get($a);
      list($a2, $b2, $c2, $d2) = explode(":", $b);
      $rt = $this->getServer()->getLevelByName($d2);
      $pos = new Position($a2, $b2, $c2, $rt);
      if ($pos === null) return;
      if ($pos instanceof Position) {
      $p->teleport($pos);
    }
      $this->getServer()->broadcastPopup("§a".$p->getName()."が§bWarp§fしました。");
      $p->sendMessage("§bXYZWARP >> §a君は§b".$d2."§aにwarpした！");
      $p->sendMessage("§bXYZWARP >> §6さぁ、楽しめよ！");
    }
    if($this->warps->exists($a)){
      $b1 = $this->warps->get($a);
      list($a11, $b11, $c11, $d11) = explode(":", $b1);
      $pp = $this->getServer()->getLevelByName($d11);
      $posb = new Position($a11, $b11, $c11, $pp);
      if ($posb === null) return;
      if ($posb instanceof Position) {
      $p->teleport($posb);
    }
      $this->getServer()->broadcastPopup("§a".$p->getName()."が§bWarp§fしました。");
      $p->sendMessage("§bXYZWARP >> §a君は§b".$d11."§aにwarpした！");
      $p->sendMessage("§bXYZWARP >> §6さぁ、楽しめよ！");
    }
  }
}
  
   }
