<?php
/**
 * System TO class
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PSI_TO
 * @author    Michael Cramer <BigMichi1@users.sourceforge.net>
 * @copyright 2009 phpSysInfo
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU General Public License version 2, or (at your option) any later version
 * @version   SVN: $Id: class.System.inc.php 255 2009-06-17 13:39:41Z bigmichi1 $
 * @link      http://phpsysinfo.sourceforge.net
 */
 /**
 * System TO class
 *
 * @category  PHP
 * @package   PSI_TO
 * @author    Michael Cramer <BigMichi1@users.sourceforge.net>
 * @copyright 2009 phpSysInfo
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU General Public License version 2, or (at your option) any later version
 * @version   Release: 3.0
 * @link      http://phpsysinfo.sourceforge.net
 */
class System
{
    /**
     * name of the host where phpSysInfo runs
     *
     * @var string
     */
    private $_hostname = "localhost";

    /**
     * ip of the host where phpSysInfo runs
     *
     * @var string
     */
    private $_ip = "127.0.0.1";

    /**
     * detailed information about the kernel
     *
     * @var string
     */
    private $_kernel = "Unknown";

    /**
     * name of the distribution
     *
     * @var string
     */
    private $_distribution = "Unknown";

    /**
     * icon of the distribution (must be available in phpSysInfo)
     *
     * @var string
     */
    private $_distributionIcon = "unknown.png";

    /**
     * detailed Information about the machine name
     *
     * @var string
     */
    private $_machine = "";

    /**
     * time in sec how long the system is running
     *
     * @var int
     */
    private $_uptime = 0;

    /**
     * count of users that are currently logged in
     *
     * @var int
     */
    private $_users = 0;

    /**
     * load of the system
     *
     * @var string
     */
    private $_load = "";

    /**
     * load of the system in percent (all cpus, if more than one)
     *
     * @var int
     */
    private $_loadPercent = null;

    /**
     * array with cpu devices
     *
     * @see CpuDevice
     *
     * @var array
     */
    private $_cpus = array();

    /**
     * array with network devices
     *
     * @see NetDevice
     *
     * @var array
     */
    private $_netDevices = array();

    /**
     * array with pci devices
     *
     * @see HWDevice
     *
     * @var array
     */
    private $_pciDevices = array();

    /**
     * array with ide devices
     *
     * @see HWDevice
     *
     * @var array
     */
    private $_ideDevices = array();

    /**
     * array with scsi devices
     *
     * @see HWDevice
     *
     * @var array
     */
    private $_scsiDevices = array();

    /**
     * array with usb devices
     *
     * @see HWDevice
     *
     * @var array
     */
    private $_usbDevices = array();

    /**
     * array with thunderbolt devices
     *
     * @see HWDevice
     *
     * @var array
     */
    private $_tbDevices = array();

    /**
     * array with I2C devices
     *
     * @see HWDevice
     *
     * @var array
     */
    private $_i2cDevices = array();

    /**
     * array with NVMe devices
     *
     * @see HWDevice
     *
     * @var array
     */
    private $_nvmeDevices = array();

    /**
     * array with Mem devices
     *
     * @see HWDevice
     *
     * @var array
     */
    private $_memDevices = array();

    /**
     * array with disk devices
     *
     * @see DiskDevice
     *
     * @var array
     */
    private $_diskDevices = array();

    /**
     * free memory in bytes
     *
     * @var int
     */
    private $_memFree = 0;

    /**
     * total memory in bytes
     *
     * @var int
     */
    private $_memTotal = 0;

    /**
     * used memory in bytes
     *
     * @var int
     */
    private $_memUsed = 0;

    /**
     * used memory by applications in bytes
     *
     * @var int
     */
    private $_memApplication = null;

    /**
     * used memory for buffers in bytes
     *
     * @var int
     */
    private $_memBuffer = null;

    /**
     * used memory for cache in bytes
     *
     * @var int
     */
    private $_memCache = null;

    /**
     * array with swap devices
     *
     * @see DiskDevice
     *
     * @var array
     */
    private $_swapDevices = array();

    /**
     * array of types of processes
     *
     * @var array
     */
    private $_processes = array();

    /**
     *  array with Virtualizer information
     *
     * @var array
     */
    private $_virtualizer = array();

    /**
     * operating system type
     *
     * @var string
     */
    private $_OS = "";

    /**
     * remove duplicate Entries and Count
     *
     * @param array $arrDev list of HWDevices
     *
     * @see HWDevice
     *
     * @return array
     */
    public static function removeDupsAndCount($arrDev)
    {
        $result = array();
        foreach ($arrDev as $dev) {
            if (count($result) === 0) {
                array_push($result, $dev);
            } else {
                $found = false;
                foreach ($result as $tmp) {
                    if ($dev->equals($tmp)) {
                        $tmp->setCount($tmp->getCount() + 1);
                        $found = true;
                        break;
                    }
                }
                if (!$found) {
                    array_push($result, $dev);
                }
            }
        }

        return $result;
    }

    /**
     * return percent of used memory
     *
     * @see System::_memUsed
     * @see System::_memTotal
     *
     * @return int
     */
    public function getMemPercentUsed()
    {
        if ($this->_memTotal > 0) {
            return round($this->_memUsed / $this->_memTotal * 100);
        } else {
            return 0;
        }
    }

    /**
     * return percent of used memory for applications
     *
     * @see System::_memApplication
     * @see System::_memTotal
     *
     * @return int
     */
    public function getMemPercentApplication()
    {
        if ($this->_memApplication !== null) {
            if (($this->_memApplication > 0) && ($this->_memTotal > 0)) {
                return round($this->_memApplication / $this->_memTotal * 100);
            } else {
                return 0;
            }
        } else {
            return null;
        }
    }

    /**
     * return percent of used memory for cache
     *
     * @see System::_memCache
     * @see System::_memTotal
     *
     * @return int
     */
    public function getMemPercentCache()
    {
        if ($this->_memCache !== null) {
            if (($this->_memCache > 0) && ($this->_memTotal > 0)) {
                if (($this->_memApplication !== null) && ($this->_memApplication > 0)) {
                    return round(($this->_memCache + $this->_memApplication) / $this->_memTotal * 100) - $this->getMemPercentApplication();
                } else {
                    return round($this->_memCache / $this->_memTotal * 100);
                }
            } else {
                return 0;
            }
        } else {
            return null;
        }
    }

    /**
     * return percent of used memory for buffer
     *
     * @see System::_memBuffer
     * @see System::_memTotal
     *
     * @return int
     */
    public function getMemPercentBuffer()
    {
        if ($this->_memBuffer !== null) {
            if (($this->_memBuffer > 0) && ($this->_memTotal > 0)) {
                if (($this->_memCache !== null) && ($this->_memCache > 0)) {
                    if (($this->_memApplication !== null) && ($this->_memApplication > 0)) {
                        return round(($this->_memBuffer + $this->_memApplication + $this->_memCache) / $this->_memTotal * 100) - $this->getMemPercentApplication() - $this->getMemPercentCache();
                    } else {
                        return round(($this->_memBuffer + $this->_memCache) / $this->_memTotal * 100) - $this->getMemPercentCache();
                    }
                } elseif (($this->_memApplication !== null) && ($this->_memApplication > 0)) {
                    return round(($this->_memBuffer + $this->_memApplication) / $this->_memTotal * 100) - $this->getMemPercentApplication();
                } else {
                    return round($this->_memBuffer / $this->_memTotal * 100);
                }
            } else {
                return 0;
            }
        } else {
            return null;
        }
    }

    /**
     * Returns total free swap space
     *
     * @see System::_swapDevices
     * @see DiskDevice::getFree()
     *
     * @return int
     */
    public function getSwapFree()
    {
        if (count($this->_swapDevices) > 0) {
            $free = 0;
            foreach ($this->_swapDevices as $dev) {
                $free += $dev->getFree();
            }

            return $free;
        }

        return null;
    }

    /**
     * Returns total swap space
     *
     * @see System::_swapDevices
     * @see DiskDevice::getTotal()
     *
     * @return int
     */
    public function getSwapTotal()
    {
        if (count($this->_swapDevices) > 0) {
            $total = 0;
            foreach ($this->_swapDevices as $dev) {
                $total += $dev->getTotal();
            }

            return $total;
        } else {
            return null;
        }
    }

    /**
     * Returns total used swap space
     *
     * @see System::_swapDevices
     * @see DiskDevice::getUsed()
     *
     * @return int
     */
    public function getSwapUsed()
    {
        if (count($this->_swapDevices) > 0) {
            $used = 0;
            foreach ($this->_swapDevices as $dev) {
                $used += $dev->getUsed();
            }

            return $used;
        } else {
            return null;
        }
    }

    /**
     * return percent of total swap space used
     *
     * @see System::getSwapUsed()
     * @see System::getSwapTotal()
     *
     * @return int
     */
    public function getSwapPercentUsed()
    {
        if ($this->getSwapTotal() !== null) {
            if ($this->getSwapTotal() > 0) {
                return round($this->getSwapUsed() / $this->getSwapTotal() * 100);
            } else {
                return 0;
            }
        } else {
            return null;
        }
    }

    /**
     * Returns $_distribution.
     *
     * @see System::$_distribution
     *
     * @return String
     */
    public function getDistribution()
    {
        return $this->_distribution;
    }

    /**
     * Sets $_distribution.
     *
     * @param String $distribution distributionname
     *
     * @see System::$_distribution
     *
     * @return void
     */
    public function setDistribution($distribution)
    {
        $this->_distribution = $distribution;
    }

    /**
     * Returns $_distributionIcon.
     *
     * @see System::$_distributionIcon
     *
     * @return String
     */
    public function getDistributionIcon()
    {
        return $this->_distributionIcon;
    }

    /**
     * Sets $_distributionIcon.
     *
     * @param String $distributionIcon distribution icon
     *
     * @see System::$_distributionIcon
     *
     * @return void
     */
    public function setDistributionIcon($distributionIcon)
    {
        $this->_distributionIcon = $distributionIcon;
    }

    /**
     * Returns $_hostname.
     *
     * @see System::$_hostname
     *
     * @return String
     */
    public function getHostname()
    {
        return $this->_hostname;
    }

    /**
     * Sets $_hostname.
     *
     * @param String $hostname hostname
     *
     * @see System::$_hostname
     *
     * @return void
     */
    public function setHostname($hostname)
    {
        $this->_hostname = $hostname;
    }

    /**
     * Returns $_ip.
     *
     * @see System::$_ip
     *
     * @return String
     */
    public function getIp()
    {
        return $this->_ip;
    }

    /**
     * Sets $_ip.
     *
     * @param String $ip IP
     *
     * @see System::$_ip
     *
     * @return void
     */
    public function setIp($ip)
    {
        $this->_ip = $ip;
    }

    /**
     * Returns $_kernel.
     *
     * @see System::$_kernel
     *
     * @return String
     */
    public function getKernel()
    {
        return $this->_kernel;
    }

    /**
     * Sets $_kernel.
     *
     * @param String $kernel kernelname
     *
     * @see System::$_kernel
     *
     * @return void
     */
    public function setKernel($kernel)
    {
        $this->_kernel = $kernel;
    }

    /**
     * Returns $_load.
     *
     * @see System::$_load
     *
     * @return String
     */
    public function getLoad()
    {
        return $this->_load;
    }

    /**
     * Sets $_load.
     *
     * @param String $load current system load
     *
     * @see System::$_load
     *
     * @return void
     */
    public function setLoad($load)
    {
        $this->_load = $load;
    }

    /**
     * Returns $_loadPercent.
     *
     * @see System::$_loadPercent
     *
     * @return int
     */
    public function getLoadPercent()
    {
        return $this->_loadPercent;
    }

    /**
     * Sets $_loadPercent.
     *
     * @param int $loadPercent load percent
     *
     * @see System::$_loadPercent
     *
     * @return void
     */
    public function setLoadPercent($loadPercent)
    {
        $this->_loadPercent = $loadPercent;
    }

    /**
     * Returns $_machine.
     *
     * @see System::$_machine
     *
     * @return String
     */
    public function getMachine()
    {
        return $this->_machine;
    }

    /**
     * Sets $_machine.
     *
     * @param string $machine machine
     *
     * @see System::$_machine
     *
     * @return void
     */
    public function setMachine($machine)
    {
        $this->_machine = $machine;
    }

    /**
     * Returns $_uptime.
     *
     * @see System::$_uptime
     *
     * @return int
     */
    public function getUptime()
    {
        return $this->_uptime;
    }

    /**
     * Sets $_uptime.
     *
     * @param integer $uptime uptime
     *
     * @see System::$_uptime
     *
     * @return void
     */
    public function setUptime($uptime)
    {
        $this->_uptime = $uptime;
    }

    /**
     * Returns $_users.
     *
     * @see System::$_users
     *
     * @return int
     */
    public function getUsers()
    {
        return $this->_users;
    }

    /**
     * Sets $_users.
     *
     * @param int $users user count
     *
     * @see System::$_users
     *
     * @return void
     */
    public function setUsers($users)
    {
        $this->_users = $users;
    }

    /**
     * Returns $_cpus.
     *
     * @see System::$_cpus
     *
     * @return array
     */
    public function getCpus()
    {
        return $this->_cpus;
    }

    /**
     * Sets $_cpus.
     *
     * @param CpuDevice $cpus cpu device
     *
     * @see System::$_cpus
     * @see CpuDevice
     *
     * @return void
     */
    public function setCpus($cpus)
    {
        array_push($this->_cpus, $cpus);
    }

    /**
     * Returns $_netDevices.
     *
     * @see System::$_netDevices
     *
     * @return array
     */
    public function getNetDevices()
    {
        if (defined('PSI_SORT_NETWORK_INTERFACES_LIST') && PSI_SORT_NETWORK_INTERFACES_LIST) {
            usort($this->_netDevices, array('CommonFunctions', 'name_natural_compare'));
        }

        return $this->_netDevices;
    }

    /**
     * Sets $_netDevices.
     *
     * @param NetDevice $netDevices network device
     *
     * @see System::$_netDevices
     * @see NetDevice
     *
     * @return void
     */
    public function setNetDevices($netDevices)
    {
        array_push($this->_netDevices, $netDevices);
    }

    /**
     * Returns $_pciDevices.
     *
     * @see System::$_pciDevices
     *
     * @return array
     */
    public function getPciDevices()
    {
        return $this->_pciDevices;
    }

    /**
     * Sets $_pciDevices.
     *
     * @param HWDevice $pciDevices pci device
     *
     * @see System::$_pciDevices
     * @see HWDevice
     *
     * @return void
     */
    public function setPciDevices($pciDevices)
    {
        array_push($this->_pciDevices, $pciDevices);
    }

    /**
     * Returns $_ideDevices.
     *
     * @see System::$_ideDevices
     *
     * @return array
     */
    public function getIdeDevices()
    {
        return $this->_ideDevices;
    }

    /**
     * Sets $_ideDevices.
     *
     * @param HWDevice $ideDevices ide device
     *
     * @see System::$_ideDevices
     * @see HWDevice
     *
     * @return void
     */
    public function setIdeDevices($ideDevices)
    {
        array_push($this->_ideDevices, $ideDevices);
    }

    /**
     * Returns $_scsiDevices.
     *
     * @see System::$_scsiDevices
     *
     * @return array
     */
    public function getScsiDevices()
    {
        return $this->_scsiDevices;
    }

    /**
     * Sets $_scsiDevices.
     *
     * @param HWDevice $scsiDevices scsi devices
     *
     * @see System::$_scsiDevices
     * @see HWDevice
     *
     * @return void
     */
    public function setScsiDevices($scsiDevices)
    {
        array_push($this->_scsiDevices, $scsiDevices);
    }

    /**
     * Returns $_usbDevices.
     *
     * @see System::$_usbDevices
     *
     * @return array
     */
    public function getUsbDevices()
    {
        return $this->_usbDevices;
    }

    /**
     * Sets $_usbDevices.
     *
     * @param HWDevice $usbDevices usb device
     *
     * @see System::$_usbDevices
     * @see HWDevice
     *
     * @return void
     */
    public function setUsbDevices($usbDevices)
    {
        array_push($this->_usbDevices, $usbDevices);
    }

    /**
     * Returns $_tbDevices.
     *
     * @see System::$_tbDevices
     *
     * @return array
     */
    public function getTbDevices()
    {
        return $this->_tbDevices;
    }

    /**
     * Sets $_tbDevices.
     *
     * @param HWDevice $tbDevices thunderbolt device
     *
     * @see System::$_tbDevices
     * @see HWDevice
     *
     * @return void
     */
    public function setTbDevices($tbDevices)
    {
        array_push($this->_tbDevices, $tbDevices);
    }

    /**
     * Returns $_i2cDevices.
     *
     * @see System::$_i2cDevices
     *
     * @return array
     */
    public function getI2cDevices()
    {
        return $this->_i2cDevices;
    }

    /**
     * Sets $_i2cDevices.
     *
     * @param HWDevice $i2cDevices I2C device
     *
     * @see System::$_i2cDevices
     * @see HWDevice
     *
     * @return void
     */
    public function setI2cDevices($i2cDevices)
    {
        array_push($this->_i2cDevices, $i2cDevices);
    }

    /**
     * Returns $_nvmeDevices.
     *
     * @see System::$_nvmeDevices
     *
     * @return array
     */
    public function getNvmeDevices()
    {
        return $this->_nvmeDevices;
    }

    /**
     * Sets $_nvmeDevices.
     *
     * @param HWDevice $nvmeDevices NVMe device
     *
     * @see System::$_nvmeDevices
     * @see HWDevice
     *
     * @return void
     */
    public function setNvmeDevices($nvmeDevices)
    {
        array_push($this->_nvmeDevices, $nvmeDevices);
    }

    /**
     * Returns $_memDevices.
     *
     * @see System::$_memDevices
     *
     * @return array
     */
    public function getMemDevices()
    {
        return $this->_memDevices;
    }

    /**
     * Sets $_memDevices.
     *
     * @param HWDevice $memDevices mem device
     *
     * @see System::$_memDevices
     * @see HWDevice
     *
     * @return void
     */
    public function setMemDevices($memDevices)
    {
        array_push($this->_memDevices, $memDevices);
    }

    /**
     * Returns $_diskDevices.
     *
     * @see System::$_diskDevices
     *
     * @return array
     */
    public function getDiskDevices()
    {
        return $this->_diskDevices;
    }

    /**
     * Sets $_diskDevices.
     *
     * @param DiskDevice $diskDevices disk device
     *
     * @see System::$_diskDevices
     * @see DiskDevice
     *
     * @return void
     */
    public function setDiskDevices($diskDevices)
    {
        array_push($this->_diskDevices, $diskDevices);
    }

    /**
     * Returns $_memApplication.
     *
     * @see System::$_memApplication
     *
     * @return int
     */
    public function getMemApplication()
    {
        return $this->_memApplication;
    }

    /**
     * Sets $_memApplication.
     *
     * @param int $memApplication application memory
     *
     * @see System::$_memApplication
     *
     * @return void
     */
    public function setMemApplication($memApplication)
    {
        $this->_memApplication = $memApplication;
    }

    /**
     * Returns $_memBuffer.
     *
     * @see System::$_memBuffer
     *
     * @return int
     */
    public function getMemBuffer()
    {
        return $this->_memBuffer;
    }

    /**
     * Sets $_memBuffer.
     *
     * @param int $memBuffer buffer memory
     *
     * @see System::$_memBuffer
     *
     * @return void
     */
    public function setMemBuffer($memBuffer)
    {
        $this->_memBuffer = $memBuffer;
    }

    /**
     * Returns $_memCache.
     *
     * @see System::$_memCache
     *
     * @return int
     */
    public function getMemCache()
    {
        return $this->_memCache;
    }

    /**
     * Sets $_memCache.
     *
     * @param int $memCache cache memory
     *
     * @see System::$_memCache
     *
     * @return void
     */
    public function setMemCache($memCache)
    {
        $this->_memCache = $memCache;
    }

    /**
     * Returns $_memFree.
     *
     * @see System::$_memFree
     *
     * @return int
     */
    public function getMemFree()
    {
        return $this->_memFree;
    }

    /**
     * Sets $_memFree.
     *
     * @param int $memFree free memory
     *
     * @see System::$_memFree
     *
     * @return void
     */
    public function setMemFree($memFree)
    {
        $this->_memFree = $memFree;
    }

    /**
     * Returns $_memTotal.
     *
     * @see System::$_memTotal
     *
     * @return int
     */
    public function getMemTotal()
    {
        return $this->_memTotal;
    }

    /**
     * Sets $_memTotal.
     *
     * @param int $memTotal total memory
     *
     * @see System::$_memTotal
     *
     * @return void
     */
    public function setMemTotal($memTotal)
    {
        $this->_memTotal = $memTotal;
    }

    /**
     * Returns $_memUsed.
     *
     * @see System::$_memUsed
     *
     * @return int
     */
    public function getMemUsed()
    {
        return $this->_memUsed;
    }

    /**
     * Sets $_memUsed.
     *
     * @param int $memUsed used memory
     *
     * @see System::$_memUsed
     *
     * @return void
     */
    public function setMemUsed($memUsed)
    {
        $this->_memUsed = $memUsed;
    }

    /**
     * Returns $_swapDevices.
     *
     * @see System::$_swapDevices
     *
     * @return array
     */
    public function getSwapDevices()
    {
        return $this->_swapDevices;
    }

    /**
     * Sets $_swapDevices.
     *
     * @param DiskDevice $swapDevices swap devices
     *
     * @see System::$_swapDevices
     * @see DiskDevice
     *
     * @return void
     */
    public function setSwapDevices($swapDevices)
    {
        array_push($this->_swapDevices, $swapDevices);
    }

    /**
     * Returns $_processes.
     *
     * @see System::$_processes
     *
     * @return array
     */
    public function getProcesses()
    {
        return $this->_processes;
    }

    /**
     * Sets $_proceses.
     *
     * @param $processes array of types of processes
     *
     * @see System::$_processes
     *
     * @return void
     */
    public function setProcesses($processes)
    {
        $this->_processes = $processes;
/*
        foreach ($processes as $proc_type=>$proc_count) {
            $this->_processes[$proc_type] = $proc_count;
        }
*/
    }

    /**
     * Returns $_virtualizer.
     *
     * @see System::$_virtualizer
     *
     * @return array
     */
    public function getVirtualizer()
    {
        return $this->_virtualizer;
    }

    /**
     * Sets $_virtualizer.
     *
     * @param String      $virtualizer virtualizername
     * @param Bool|String $value       true, false or virtualizername to replace
     *
     * @see System::$_virtualizer
     *
     * @return void
     */
    public function setVirtualizer($virtualizer, $value = true)
    {
        if (!isset($this->_virtualizer[$virtualizer])) {
            if (is_bool($value)) {
                $this->_virtualizer[$virtualizer] = $value;
            } else { // replace the virtualizer with another
                $this->_virtualizer[$virtualizer] = true;
                $this->_virtualizer[$value] = false;
            }
        }
    }

    /**
     * Returns $_OS.
     *
     * @see System::$_OS
     *
     * @return string
     */
    public function getOS()
    {
        return $this->_OS;
    }

    /**
     * Sets $_OS.
     *
     * @param $os operating system type
     *
     * @see System::$_OS
     *
     * @return void
     */
    public function setOS($OS)
    {
        $this->_OS = $OS;
    }
}
