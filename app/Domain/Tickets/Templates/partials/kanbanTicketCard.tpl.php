<?php
$descriptionText = trim(strip_tags($row['description'] ?? ''));
$editorFullName = trim(($row['editorFirstname'] ?? '') . ' ' . ($row['editorLastname'] ?? ''));
$tagsText = is_string($row['tags'] ?? null) ? $row['tags'] : '';
$allColumns = $tpl->get('allKanbanColumns');
$columnKeys = array_keys($allColumns);
$isFirstColumn = $key === $columnKeys[0];
$isLastColumn = $key === end($columnKeys);
?>
<div
    class="ticketBox moveable container priority-border-<?= $row['priority']?>"
    id="ticket_<?php echo $row['id']; ?>"
    data-ticket-id="<?= $row['id']; ?>"
    data-headline="<?= $tpl->escape($row['headline']); ?>"
    data-description="<?= $tpl->escape($descriptionText); ?>"
    data-tags="<?= $tpl->escape($tagsText); ?>"
    data-editor-name="<?= $tpl->escape($editorFullName); ?>"
    data-editor-id="<?= $tpl->escape($row['editorId'] ?? ''); ?>"
    data-kanban-sort-index="<?= (int) ($row['kanbanSortIndex'] ?? 0); ?>"
>
    <div class="row">
        <div class="col-md-12">
            <?php echo app('blade.compiler')::render('@include("tickets::partials.ticketsubmenu", [
                "ticket" => $ticket,
                "onTheClock" => $onTheClock,
                "isFirstColumn" => $isFirstColumn,
                "isLastColumn" => $isLastColumn
            ])', ['ticket' => $row, 'onTheClock' => $tpl->get('onTheClock'), 'isFirstColumn' => $isFirstColumn, 'isLastColumn' => $isLastColumn]); ?>

            <?php if ($row['dependingTicketId'] > 0) { ?>
                <small><a href="#/tickets/showTicket/<?= $row['dependingTicketId'] ?>" class="form-modal"><?= $tpl->escape($row['parentHeadline']) ?></a></small> //
            <?php } ?>
            <small><i class="fa <?php echo $todoTypeIcons[strtolower($row['type'])]; ?>"></i></small>
            <small><?php
                $useIncremental = isset($row['incrementalTicketId']) && (int) $row['incrementalTicketId'] === 1;
                $displayNum = $useIncremental && isset($row['projectTicketNumber']) ? (int) $row['projectTicketNumber'] : $row['id'];
                $prefix = !empty($row['projectKey']) ? $row['projectKey'] . '-' : '#';
                echo $prefix . $displayNum;
            ?></small>
            <div class="kanbanCardContent">
                <h4>
                    <?php if (isset($row['pinned']) && $row['pinned']) { ?>
                        <i class="fa fa-thumbtack" style="color: #dc3545; margin-right: 5px; transform: rotate(45deg);" data-tippy-content="Pinned to top"></i>
                    <?php } ?>
                    <a href="#/tickets/showTicket/<?php echo $row['id']; ?>" data-hx-get="<?= BASE_URL?>/tickets/showTicket/<?php echo $row['id']; ?>" hx-swap="none" preload="mouseover"><?php $tpl->e($row['headline']); ?></a>
                </h4>

                <div class="kanbanContent" style="margin-bottom: 20px">
                    <?php echo $tpl->escapeMinimalRemoveImage($row['description']); ?>
                </div>
            </div>
            <div class="tw-flex">
                <?php if ($row['dateToFinish'] != '0000-00-00 00:00:00' && $row['dateToFinish'] != '1969-12-31 00:00:00') { ?>
                    <div>
                        <?php echo $tpl->__('label.due_icon'); ?>
                        <input type="text" title="<?php echo $tpl->__('label.due'); ?>" value="<?php echo format($row['dateToFinish'])->date() ?>" class="duedates secretInput" style="margin-left:0px;" data-id="<?php echo $row['id']; ?>" name="date" />
                    </div>
                    <div>
                        <?php $tpl->dispatchTplEvent('afterDates', ['ticket' => $row]); ?>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>

    <div class="clearfix" style="padding-bottom: 8px;"></div>

    <div class="timerContainer " id="timerContainer-<?php echo $row['id']; ?>">
        <div class="dropdown ticketDropdown milestoneDropdown colorized show firstDropdown" style="max-width: 100%;">
            <a style="background-color:<?= $tpl->escape($row['milestoneColor'])?>;max-width: 100%;display: inline-block;" class="dropdown-toggle f-left label-default milestone" href="javascript:void(0);" role="button" id="milestoneDropdownMenuLink<?= $row['id']?>" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="text" style="display: inline-block; max-width: 80%; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; vertical-align: middle;"><?php
                if ($row['milestoneid'] != '' && $row['milestoneid'] != 0) {
                    $tpl->e($row['milestoneHeadline']);
                } else {
                    echo $tpl->__('label.no_milestone');
                }?>
                </span>
                &nbsp;<i class="fa fa-caret-down" aria-hidden="true"></i>
            </a>
            <ul class="dropdown-menu" aria-labelledby="milestoneDropdownMenuLink<?= $row['id']?>" style="position: absolute; z-index: 9999;">
                <li class="nav-header border"><?= $tpl->__('dropdown.choose_milestone')?></li>
                <li class='dropdown-item'><a style='background-color:#b0b0b0' href='javascript:void(0);' data-label="<?= $tpl->__('label.no_milestone')?>" data-value='<?= $row['id'].'_0_#b0b0b0'?>'> <?= $tpl->__('label.no_milestone')?> </a></li>

                <?php foreach ($tpl->get('milestones') as $milestone) {
                    echo "<li class='dropdown-item'>
                        <a href='javascript:void(0);' data-label='".$tpl->escape($milestone->headline)."' data-value='".$row['id'].'_'.$milestone->id.'_'.$tpl->escape($milestone->tags)."' id='ticketMilestoneChange".$row['id'].$milestone->id."' style='background-color:".$tpl->escape($milestone->tags)."'>".$tpl->escape($milestone->headline).'</a>';
                    echo '</li>';
                }?>
            </ul>
        </div>

        <?php if ($row['storypoints'] != '' && $row['storypoints'] > 0) { ?>
            <div class="dropdown ticketDropdown effortDropdown show">
                <a class="dropdown-toggle f-left label-default effort" href="javascript:void(0);" role="button" id="effortDropdownMenuLink<?= $row['id']?>" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <span class="text"><?php
                    if ($row['storypoints'] != '' && $row['storypoints'] > 0) {
                        echo $efforts[''.$row['storypoints']] ?? $row['storypoints'];
                    } else {
                        echo $tpl->__('label.story_points_unkown');
                    }?>
                    </span>
                    &nbsp;<i class="fa fa-caret-down" aria-hidden="true"></i>
                </a>
                <ul class="dropdown-menu" aria-labelledby="effortDropdownMenuLink<?= $row['id']?>">
                    <li class="nav-header border"><?= $tpl->__('dropdown.how_big_todo')?></li>
                    <?php foreach ($efforts as $effortKey => $effortValue) {
                        echo "<li class='dropdown-item'>
                            <a href='javascript:void(0);' data-value='".$row['id'].'_'.$effortKey."' id='ticketEffortChange".$row['id'].$effortKey."'>".$effortValue.'</a>';
                        echo '</li>';
                    }?>
                </ul>
            </div>
        <?php } ?>

        <div class="dropdown ticketDropdown priorityDropdown show">
            <a class="dropdown-toggle f-left label-default priority priority-bg-<?= $row['priority']?>" href="javascript:void(0);" role="button" id="priorityDropdownMenuLink<?= $row['id']?>" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="text"><?php
                if ($row['priority'] != '' && $row['priority'] > 0) {
                    echo $priorities[$row['priority']] ?? $tpl->__('label.priority_unkown');
                } else {
                    echo $tpl->__('label.priority_unkown');
                }?>
                </span>
                &nbsp;<i class="fa fa-caret-down" aria-hidden="true"></i>
            </a>
            <ul class="dropdown-menu" aria-labelledby="priorityDropdownMenuLink<?= $row['id']?>">
                <li class="nav-header border"><?= $tpl->__('dropdown.select_priority')?></li>
                <?php foreach ($priorities as $priorityKey => $priorityValue) {
                    echo "<li class='dropdown-item'>
                        <a href='javascript:void(0);' class='priority-bg-".$priorityKey."' data-value='".$row['id'].'_'.$priorityKey."' id='ticketPriorityChange".$row['id'].$priorityKey."'>".$priorityValue.'</a>';
                    echo '</li>';
                }?>
            </ul>
        </div>

        <div class="dropdown ticketDropdown userDropdown noBg show right lastDropdown dropRight">
            <a class="dropdown-toggle f-left" href="javascript:void(0);" role="button" id="userDropdownMenuLink<?= $row['id']?>" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="text">
                    <?php
                    if ($row['editorFirstname'] != '') {
                        echo "<span id='userImage".$row['id']."'><img src='".BASE_URL.'/api/users?profileImage='.$row['editorId']."' width='25' style='vertical-align: middle;'/></span>";
                    } else {
                        echo "<span id='userImage".$row['id']."'><img src='".BASE_URL."/api/users?profileImage=false' width='25' style='vertical-align: middle;'/></span>";
                    }?>
                </span>
            </a>
            <ul class="dropdown-menu" aria-labelledby="userDropdownMenuLink<?= $row['id']?>">
                <li class="nav-header border"><?= $tpl->__('dropdown.choose_user')?></li>
                <?php
                if (is_array($tpl->get('users'))) {
                    foreach ($tpl->get('users') as $user) {
                        echo "<li class='dropdown-item'>
                            <a href='javascript:void(0);' data-label='".sprintf(
                            $tpl->__('text.full_name'),
                            $tpl->escape($user['firstname']),
                            $tpl->escape($user['lastname'])
                        )."' data-value='".$row['id'].'_'.$user['id'].'_'.$user['profileId']."' id='userStatusChange".$row['id'].$user['id']."' ><img src='".BASE_URL.'/api/users?profileImage='.$user['id']."' width='25' style='vertical-align: middle; margin-right:5px;'/>".sprintf(
                            $tpl->__('text.full_name'),
                            $tpl->escape($user['firstname']),
                            $tpl->escape($user['lastname'])
                        ).'</a>';
                        echo '</li>';
                    }
                }?>
            </ul>
        </div>
    </div>
    <div class="clearfix"></div>

    <?php if ($row['commentCount'] > 0 || $row['subtaskCount'] > 0 || $row['tags'] != '') {?>
        <div class="row">
            <div class="col-md-12 border-top" style="white-space: nowrap;">
                <?php if ($row['commentCount'] > 0) {?>
                    <a href="#/tickets/showTicket/<?php echo $row['id']; ?>"><span class="fa-regular fa-comments"></span> <?php echo $row['commentCount'] ?></a>&nbsp;
                <?php } ?>

                <?php if ($row['subtaskCount'] > 0) {?>
                    <a id="subtaskLink_<?php echo $row['id']; ?>" href="#/tickets/showTicket/<?php echo $row['id']; ?>" class="subtaskLineLink"> <span class="fa fa-diagram-successor"></span> <?php echo $row['subtaskCount'] ?></a>&nbsp;
                <?php } ?>
                <?php if ($row['tags'] != '') {?>
                    <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-tags" aria-hidden="true"></i>
                    </a>
                    <?php
                    $tagsArray = explode(',', $row['tags']);
                    $tags = array_slice($tagsArray, 0, 3);
                    $tags = array_map(fn($tag) => htmlspecialchars(trim($tag)), $tags);
                    echo implode(', ', $tags);
                    ?>
                    <ul class="dropdown-menu ">
                        <li style="padding:10px"><div class='tagsinput readonly'>
                        <?php
                        foreach ($tagsArray as $tag) {
                            echo "<span class='tag'><span>".$tpl->escape($tag).'</span></span>';
                        }
                    ?>
                        </div></li>
                    </ul>
                <?php } ?>
            </div>
        </div>
    <?php } ?>
</div>
