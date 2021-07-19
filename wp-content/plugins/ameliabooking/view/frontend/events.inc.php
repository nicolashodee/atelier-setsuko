<?php
/**
 * @copyright © TMS-Plugins. All rights reserved.
 * @licence   See LICENCE.md for license details.
 */

?>
<script>
  var hasEventShortcode = (typeof hasEventShortcode === 'undefined') ? false : true;
  var bookingEntitiesIds = (typeof bookingEntitiesIds === 'undefined') ? [] : bookingEntitiesIds;
  bookingEntitiesIds.push(
    {
      'hasApiCall': <?php echo $atts['hasApiCall']; ?>,
      'trigger': '<?php echo $atts['trigger']; ?>',
      'counter': '<?php echo $atts['counter']; ?>',
      'eventId': '<?php echo $atts['event']; ?>',
      'eventRecurring': <?php echo $atts['recurring'] ? 1 : 0; ?>,
      'eventTag': '<?php echo $atts['tag']; ?>'
    }
  );
  var lazyBookingEntitiesIds = (typeof lazyBookingEntitiesIds === 'undefined') ? [] : lazyBookingEntitiesIds;
  if (bookingEntitiesIds[bookingEntitiesIds.length - 1].trigger !== '') {
    lazyBookingEntitiesIds.push(bookingEntitiesIds.pop());
  }
</script>

<div id="amelia-app-booking<?php echo $atts['counter']; ?>" class="amelia-service amelia-frontend amelia-app-booking<?php echo $atts['trigger'] !== '' ? ' amelia-skip-load amelia-skip-load-' . $atts['counter'] : ''; ?>">
    <?php echo $atts['type'] && $atts['type'] === 'calendar' ? '<events-calendar></events-calendar>' : '<events-list></events-list>'; ?>
</div>
