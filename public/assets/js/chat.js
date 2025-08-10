'use strict';

(function () {

  // Applying perfect-scrollbar 
  if (document.querySelector('.chat-aside .tab-content #chats')) {
    const sidebarBodyScroll = new PerfectScrollbar('.chat-aside .tab-content #chats');
  }

  if (document.querySelector('.chat-aside .tab-content #calls')) {
    const sidebarBodyScroll = new PerfectScrollbar('.chat-aside .tab-content #calls');
  }

  if (document.querySelector('.chat-aside .tab-content #contacts')) {
    const sidebarBodyScroll = new PerfectScrollbar('.chat-aside .tab-content #contacts');
  }

  if (document.querySelector('.chat-content .chat-body')) {
    const sidebarBodyScroll = new PerfectScrollbar('.chat-content .chat-body');
  }



  // Show/hide 'chat-content' on small screen devices (max-width: 991px)
  const chatListItem = document.querySelectorAll('.chat-list .chat-item');
  const chatContent = document.querySelector('.chat-content');
  const backToChatListButton = document.querySelector('#backToChatList');

  chatListItem.forEach((item) => {
    item.addEventListener('click', () => {
      chatContent.classList.toggle('show');
    });
  });

  backToChatListButton.addEventListener('click', () => {
    chatContent.classList.toggle('show');
  });

})();