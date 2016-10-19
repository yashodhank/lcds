// Update field contents by retrieving field data from backend
function updateContents(f) {
  if (!f.canUpdate) {
    return;
  }
  $.get(f.url, function(j) {
    if (j.success) {
      setContents(f, j.next);
    } else {
      f.$field.text('error');
    }
  });
}

// Update screen and reload if necessary
var updateScreenUrl;
var lastChanges = null;
var pleaseDie = false;
function updateScreen() {
  $.get(updateScreenUrl, function(j) {
    if (j.success) {
      if (lastChanges == null) {
        lastChanges = j.data;
      } else if (lastChanges != j.data) {
        end();
      }
    }
  });
}

// Kill current screen and reload after fields timeouts
var remaining = 0;
function end() {
  if (pleaseDie) {
    return;
  }
  pleaseDie = true;
  for (var f in fields) {
    if (fields[f].timeout) {
      remaining++;
    }
  }
}

// jQuery load
// Initialize frontend handlers
// Create fields and setup content updates timers & screen update timer
function onLoad() {
  // Init
  $('.field').each(function() {
    var $f = $(this);
    var f = {
      $field: $f,
      id: $f.attr('data-id'),
      url: $f.attr('data-url'),
      types: $f.attr('data-types').split(' '),
      canUpdate: false,
      previous: null,
      current: null,
      next: null,
      timeout: null,
    };

    f.canUpdate = f.url != null;
    fields.push(f);
    updateContents(f);
  });

  // Setup content updates loop
  setInterval(function() {
    for (var f in fields) {
      updateContents(fields[f]);
    }
    updateScreen();
  }, 60000);
  updateScreen();
}

// Assign content to field and force next occurrence if necessary
function setContents(f, contents) {
  f.contents = contents;
  if (!f.timeout && contents.length) {
    next(f);
  }
}

// Find next content to display for field
// Take a random content from field contents
function next(f) {
  if (pleaseDie) { // Stoping screen
    if (--remaining <= 0) {
      return window.location.reload();
    }
    return;
  }
  f.previous = f.current;
  f.current = null;
  var pData = f.previous && f.previous.data;
  // Avoid repeat & other field same content
  //for (content in f.contents) {
  while (true) {
    var c = f.contents[Math.floor(Math.random() * f.contents.length)];
    if (c.data == pData) {
      // Will repeat, avoid if enough content
      if (f.contents.length < 2) {
        f.next = c;
        break;
      }
    } else if (fields.filter(function(field) {
        return field.current && field.current.data == c.data;
      }).length) {
      // Same content already displayed on other field, avoid if enough content
      if (f.contents.length < 3) {
        f.next = c;
        break;
      }
    } else {
      f.next = c;
      break;
    }
  }

  if (!f.next && f.contents.length > 0) {
    f.next = f.contents[0];
  }
  updateFieldContent(f);
}

// Update field displayed content
// Setup next timeout based on content duration
function updateFieldContent(f) {
  if (f.next && f.next.duration > 0) {
    f.current = f.next
    f.next = null;
    f.$field.html(f.current.data);
    f.$field.show();
    if (f.$field.text() != '') {
      f.$field.textfill({
        maxFontPixels: 0,
      });
    }
    if (f.timeout) {
      clearTimeout(f.timeout);
    }
    f.timeout = setTimeout(function() {
      next(f);
    }, f.current.duration * 1000);
  } else {
    f.timeout = null;
    console.error('Cannot set content', f);
  }
}

var fields = [];
$(onLoad);
