<?php

defined('RESTRICTED') or exit('Restricted access');

echo $tpl->displayNotification();
foreach ($__data as $var => $val) {
    $$var = $val; // necessary for blade refactor
}
$tickets = $tpl->get('tickets');
$sprints = $tpl->get('sprints');
$searchCriteria = $tpl->get('searchCriteria');
$currentSprint = $tpl->get('currentSprint');

$todoTypeIcons = $tpl->get('ticketTypeIcons');

$efforts = $tpl->get('efforts');
$priorities = $tpl->get('priorities');

$allTicketGroups = $tpl->get('allTickets');

?>

<?php $tpl->displaySubmodule('tickets-ticketHeader') ?>

<?php /* Legacy kanban search CSS - removed but kept as comment for reference
<style>
    .kanban-search-container {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 82%;
    }

    .kanban-search-input {
        position: relative;
        width: 100%;
        max-width: 340px;
    }

    .kanban-search-input input {
        height: 35px;
        line-height: 40px;
        padding: 0 16px;
        border: 1px solid #d5d5d5;
        border-radius: 22px;
        font-size: 14px;
        background-color: #fff;
        color: var(--primary-font-color);
        transition: border-color 0.2s ease;
        position: relative;
        z-index: 1;
    }

    .kanban-search-input input:focus {
        border-color: var(--primary-color);
        outline: none;
        box-shadow: 0 0 0 2px rgba(0, 94, 168, 0.1);
    }

    .kanban-search-clear {
        position: absolute;
        right: 8px;
        top: 50%;
        transform: translateY(-50%);
        border: none;
        background: none;
        color: #6c757d;
        cursor: pointer;
        padding: 6px;
        border-radius: 50%;
        display: none;
        z-index: 10;
        line-height: 1;
        font-size: 14px;
    }

    .kanban-search-input.has-value .kanban-search-clear {
        display: block;
    }

    .kanban-search-clear:hover {
        color: var(--primary-color);
        background-color: rgba(0, 0, 0, 0.05);
    }

    .kanban-search-hidden {
        display: none !important;
    }

    @media (max-width: 991px) {
        .kanban-search-container {
            margin-top: 15px;
            margin-bottom: 10px;
        }
    }
</style>
*/ ?>

<div class="maincontent">

    <?php $tpl->displaySubmodule('tickets-ticketBoardTabs') ?>
    <div class="maincontentinner kanban-board-wrapper" style="overflow:auto;">
         <div class="row">
            <div class="col-md-4">
                <?php
                $tpl->dispatchTplEvent('filters.afterLefthandSectionOpen');

$tpl->displaySubmodule('tickets-ticketNewBtn');
$tpl->displaySubmodule('tickets-ticketFilter');

$tpl->dispatchTplEvent('filters.beforeLefthandSectionClose');
?>
            </div>

            <?php /* Legacy kanban search HTML - removed but kept as comment for reference
            <div class="col-md-4 center">
                <div class="kanban-search-container">
                    <div class="kanban-search-input" id="kanbanSearchWrapper">
                        <input
                            type="search"
                            id="kanbanBoardSearch"
                            value="<?= $tpl->escape($searchCriteria['term'] ?? '') ?>"
                            placeholder="<?= $tpl->__('label.search_term') ?>"
                            aria-label="<?= $tpl->__('label.search_term') ?>"
                            autocomplete="off"
                        />
                        <button type="button" id="kanbanBoardSearchClear" class="kanban-search-clear" aria-label="Clear search">
                            <span class="fa fa-times" aria-hidden="true"></span>
                        </button>
                    </div>
                </div>
            </div>
            */ ?>
            <div class="col-md-4">

            </div>
            <div class="col-md-4">
                <?php $tpl->displaySubmodule('tickets-kanbanSearchBar') ?>
            </div>
        </div>

        <div class="clearfix"></div>


        <?php if (isset($allTicketGroups['all'])) {
            $allTickets = $allTicketGroups['all']['items'];
        }
?>
        <div class="" style="
            display: flex;
            position: sticky;
            justify-content: flex-start;
            z-index: 9;
            ">
        <?php foreach ($tpl->get('allKanbanColumns') as $key => $statusRow) { ?>
            <div class="column">

                <h4 class="widgettitle title-primary title-border-<?php echo $statusRow['class']; ?>">

                    <?php if ($login::userIsAtLeast($roles::$manager)) { ?>
                        <div class="inlineDropDownContainer" style="float:right;">
                            <a href="javascript:void(0);" class="dropdown-toggle ticketDropDown editHeadline" data-toggle="dropdown">
                                <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                            </a>

                            <ul class="dropdown-menu">
                                <li><a href="#/setting/editBoxLabel?module=ticketlabels&label=<?= $key?>" class="editLabelModal"><?= $tpl->__('headlines.edit_label')?></a>
                                </li>
                                <li><a href="<?= BASE_URL ?>/projects/showProject/<?= session('currentProject'); ?>#todosettings"><?= $tpl->__('links.add_remove_col')?></a></li>
                            </ul>
                        </div>
                    <?php } ?>

                    <strong class="count">0</strong>
                    <?php $tpl->e($statusRow['name']); ?>

                </h4>

                <div class="">
                    <a href="javascript:void(0);"
                       style="padding:10px; display:block; width:100%;" id="ticket_new_link_<?= $key?>"
                       onclick="jQuery('#ticket_new_link_<?= $key?>').toggle('fast'); jQuery('#ticket_new_<?= $key?>').toggle('fast', function() { jQuery(this).find('input[name=headline]').focus(); });">
                        <i class="fas fa-plus-circle"></i> Add To-Do</a>

                     <div class="hideOnLoad " id="ticket_new_<?= $key?>" style="padding-top:5px; padding-bottom:5px;">

                        <form method="post">
                            <input type="text" name="headline" style="width:100%;" placeholder="Enter To-Do Title" title="<?= $tpl->__('label.headline') ?>"/><br />
                            <input type="hidden" name="milestone" value="<?php echo $searchCriteria['milestone']; ?>" />
                            <input type="hidden" name="status" value="<?php echo $key; ?> " />
                            <input type="hidden" name="sprint" value="<?php echo session('currentSprint'); ?> " />
                            <input type="submit" value="Save" name="quickadd" />
                            <a href="javascript:void(0);" class="btn btn-default" onclick="jQuery('#ticket_new_<?= $key?>, #ticket_new_link_<?= $key?>').toggle('fast');">
                                <?= $tpl->__('links.cancel') ?>
                            </a>
                        </form>

                        <div class="clearfix"></div>
                    </div>

                </div>
            </div>
        <?php } ?>
        </div>

        <?php foreach ($allTicketGroups as $group) {?>
             <?php
        $allTickets = $group['items'];
        $statusCounts = $tpl->get('kanbanStatusCounts') ?: [];
        if (empty($statusCounts)) {
            foreach ($allTickets as $ticketItem) {
                $statusKey = (string) ($ticketItem['status'] ?? '');
                if (! isset($statusCounts[$statusKey])) {
                    $statusCounts[$statusKey] = 0;
                }
                $statusCounts[$statusKey]++;
            }
        }
            ?>

            <?php if ($group['label'] != 'all') { ?>
                <h5 class="accordionTitle kanbanLane <?= $group['class']?>" id="accordion_link_<?= $group['id'] ?>">
                    <a href="javascript:void(0)" class="accordion-toggle" id="accordion_toggle_<?= $group['id'] ?>" onclick="leantime.snippets.accordionToggle('<?= $group['id'] ?>');">
                        <i class="fa fa-angle-down"></i><?= $group['label'] ?> (<?= count($group['items']) ?>)
                    </a>
                    <br />
                    <small style="padding-left:20px; color:var(--primary-font-color); font-size:var(--font-size-s);"><?= $group['more-info'] ?></small>
                </h5>
                <div class="simpleAccordionContainer kanban" id="accordion_content-<?= $group['id'] ?>">
            <?php } ?>

                    <div class="sortableTicketList kanbanBoard" id="kanboard-<?= $group['id'] ?>" style="margin-top:-5px;">

                        <div class="row-fluid">

                            <?php foreach ($tpl->get('allKanbanColumns') as $key => $statusRow) { ?>
                            <div class="column">
                                <div class="contentInner <?php echo 'status_'.$key; ?>" style="min-width: 190px;">
                                    <?php $visibleLimit = (int) ($tpl->get('kanbanPageSize') ?: 50); ?>
                                    <?php $renderedCount = 0; ?>
                                    <?php $columnTotal = (int) ($statusCounts[(string) $key] ?? 0); ?>
                                    <?php foreach ($allTickets as $row) { ?>
                                        <?php if ($row['status'] == $key) {?>
                                        <?php $renderedCount++; ?>
                                        <?php include __DIR__ . '/partials/kanbanTicketCard.tpl.php'; ?>
                                        <?php } ?>
                                    <?php } ?>

                                    <?php if ($columnTotal > $visibleLimit) { ?>
                                        <div class="kanban-load-more-container" style="padding: 8px 4px; text-align: center;">
                                            <button
                                                type="button"
                                                class="btn btn-default kanban-load-more"
                                                data-board-id="<?= $tpl->escape($group['id']) ?>"
                                                data-status-id="<?= $tpl->escape((string) $key) ?>"
                                                data-batch-size="50"
                                                data-visible-count="<?= min($visibleLimit, $columnTotal) ?>"
                                                data-total-count="<?= $columnTotal ?>"
                                            >
                                                Load more (<?= min($visibleLimit, $columnTotal) ?>/<?= $columnTotal ?>)
                                            </button>
                                        </div>
                                    <?php } ?>
                                </div>

                            </div>
                        <?php } ?>
                            <div class="clearfix"></div>

                        </div>
                    </div>

            <?php if ($group['label'] != 'all') { ?>
                </div>
            <?php } ?>

        <?php } ?>

    </div>

</div>

<script type="text/javascript">

    jQuery(document).ready(function(){

    <?php if ($login::userIsAtLeast($roles::$editor)) { ?>
        leantime.ticketsController.initUserDropdown();
        leantime.ticketsController.initMilestoneDropdown();
        leantime.ticketsController.initDueDateTimePickers();
        leantime.ticketsController.initEffortDropdown();
        leantime.ticketsController.initPriorityDropdown();


        var ticketStatusList = [<?php foreach ($tpl->get('allTicketStates') as $key => $statusRow) {
            echo "'".$key."',";
        }?>];
        leantime.ticketsController.initTicketKanban(ticketStatusList);

    <?php } else { ?>
        leantime.authController.makeInputReadonly(".maincontentinner");
    <?php } ?>

    leantime.ticketsController.setUpKanbanColumns();
    leantime.ticketsController.initKanbanProgressiveLoading();
    <?php /* Legacy kanban search initialization - removed but kept as comment for reference
	leantime.ticketsController.initKanbanSearch();
    */ ?>
	// leantime.ticketsController.initKanbanSearch(); // Moved to kanbanSearchBar.sub.php for proper async loading

        <?php if (isset($_GET['showTicketModal'])) {
            if ($_GET['showTicketModal'] == '') {
                $modalUrl = '';
            } else {
                $modalUrl = '/'.(int) $_GET['showTicketModal'];
            }
            ?>

        leantime.ticketsController.openTicketModalManually("<?= BASE_URL ?>/tickets/showTicket<?php echo $modalUrl; ?>");
        window.history.pushState({},document.title, '<?= BASE_URL ?>/tickets/showKanban');

        <?php } ?>


        <?php foreach ($allTicketGroups as $group) {

            foreach ($group['items'] as $ticket) {
                if ($ticket['dependingTicketId'] > 0) {
                    ?>
            var startElement =  document.getElementById('subtaskLink_<?= $ticket['dependingTicketId']; ?>');
            var endElement =  document.getElementById('ticket_<?= $ticket['id']; ?>');


            if ( startElement != undefined && endElement != undefined) {

                var startAnchor = LeaderLine.mouseHoverAnchor({
                    element: startElement,
                    showEffectName: 'draw',
                    style: {background: 'none', backgroundColor: 'none'},
                    hoverStyle: {background: 'none', backgroundColor: 'none', cursor: 'pointer'}
                });

                var line<?= $ticket['id'] ?> = new LeaderLine(startAnchor, endElement, {
                    startPlugColor: 'var(--accent1)',
                    endPlugColor: 'var(--accent2)',
                    gradient: true,
                    size: 2,
                    path: "grid",
                    startSocket: 'bottom',
                    endSocket: 'auto'
                });

                jQuery("#ticket_<?= $ticket['id'] ?>").mousedown(function () {

                })
                    .mousemove(function () {

                    })
                    .mouseup(function () {
                        line<?= $ticket['id'] ?>.position();
                    });

                jQuery("#ticket_<?= $ticket['dependingTicketId'] ?>").mousedown(function () {

                    })
                    .mousemove(function () {


                    })
                    .mouseup(function () {
                        line<?= $ticket['id'] ?>.position();

                    });

            }

                <?php }
                }
        } ?>




    });
</script>
