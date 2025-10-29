// Toggle inventory for smaller devices where inventory is not visible
export const inventorySidebarMob = {
  toggleInventory() {
    const inventory = document.getElementById('inventory');
    if (inventory.style.visibility === 'hidden') {
      console.log('hlelo');
      inventory.style.width = '50%';
      inventory.style.visibility = 'visible';
    } else {
      if (inventory.querySelectorAll('#stck_menu').length > 0) {
        const element = <HTMLElement>(
          inventory.querySelectorAll('#stck_menu')[0]
        );
        element.style.visibility = 'hidden';
      }
      inventory.style.width = '10%';
      inventory.style.visibility = 'hidden';
    }
  },
};
