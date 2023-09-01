class AlertMessages {

    constructor() {
      this.messages = [];
    }
  
    addError(message) {
      this.messages.push({ type: 'error', message: message });
    }
  
    addWarning(message) {
      this.messages.push({ type: 'warning', message: message });
    }
  
    addSuccess(message) {
      this.messages.push({ type: 'success', message: message });
    }
  
    getMessageColor(type) {
      switch (type) {
        case 'error':
          return 'red';
        case 'warning':
          return 'orange';
        case 'success':
          return 'green';
        default:
          return 'black';
      }
    }
  
    displayMessages() {
      const container = document.createElement('div');
      container.style.position = 'fixed';
      container.style.bottom = '50px';
      container.style.left = '50%';
      container.style.transform = 'translateX(-50%)';
      container.style.backgroundColor = '#fff';
      container.style.border = '1px solid #ccc';
      container.style.padding = '10px';
      container.style.textAlign = 'center';
  
      this.messages.forEach((msg) => {
        const messageElement = document.createElement('div');
        messageElement.style.color = this.getMessageColor(msg.type);
        messageElement.innerText = msg.message;
        container.appendChild(messageElement);
      });
  
      document.body.appendChild(container);
  
      setTimeout(() => {
        // Efface le message après 3 secondes
        container.remove();
      }, 4000);
  
  this.messages = []; // Clear the messages after displaying
  }
}
// Create a global instance of the AlertMessages class
const alertMessages = new AlertMessages();

// Function to add messages from PHP code
function addMessageFromPHP(type, message) {
  console.log('appel de message : ', message);

  switch (type) {
      case 'error':
          alertMessages.addError(message);
          break;
      case 'warning':
          alertMessages.addWarning(message);
          break;
      case 'success':
          alertMessages.addSuccess(message);
          break;
      default:
          alertMessages.addError('Invalid message type.');
          break;
  }

  alertMessages.displayMessages();
}