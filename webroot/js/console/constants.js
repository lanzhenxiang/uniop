var vmware = (typeof vmware == "undefined" || !vmware) ? {} : vmware;

/**
 * Enumeration of events as strings to be used in conjunction with an event manager.
 */
vmware.events = {
    VIEW_CONNECTION_STATE_CHANGE: 'viewconnectionstatechange',
    SIZE_CHANGE: 'sizechange',
    WINDOW_STATE_CHANGE: 'windowstatechange',
    GRAB_STATE_CHANGE: 'grabstatechange',
    VIEW_MESSAGE: 'viewmessage'
};

/**
 * Enumerator of installer files.
 */
vmware.installers = {
    WINDOWS: 'vmware-vmrc-win32-x86.exe',
    LINUX: {x86: 'VMware-VMRC.i386.bundle', x86_64: 'VMware-VMRC.x86_64.bundle'}
};

/**
 * Constants used by the plugin 'object' DOM element.
 */
vmware.object = {
	    CLASSID: 'CLSID:4AEA1010-0A0C-405E-9B74-767FC8A998CB',
	    TYPE: 'application/x-vmware-remote-console-2012',
	    VERSION: '5.5.0.1280474',
	    API: 'vsphere-2012'
	};
